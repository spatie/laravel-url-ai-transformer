<?php

namespace Spatie\LaravelUrlAiTransformer\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelUrlAiTransformer\Events\TransformerEnded;
use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;
use Spatie\LaravelUrlAiTransformer\Events\TransformerStarted;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class ProcessTransformerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $transformerClass,
        public string $url,
        public string $urlContent
    ) {}

    public function handle(): void
    {
        $transformer = new $this->transformerClass;

        try {
            $this->processTransformer($transformer);
        } catch (Exception $exception) {
            $transformationResult = $this->getTransformationResult($transformer);

            $transformationResult->recordException($exception);

            event(new TransformerFailed($transformer, $transformationResult, $exception));
        }
    }

    protected function processTransformer(Transformer $transformer): void
    {
        $transformationResult = $this->getTransformationResult($transformer);

        $transformer->setTransformationProperties($this->url, $this->urlContent, $transformationResult);

        if (! $transformer->shouldRun()) {
            return;
        }

        event(new TransformerStarted($transformer, $transformationResult, $this->url, $this->urlContent));

        $transformer->transform();

        event(new TransformerEnded($transformer, $transformationResult, $this->url, $this->urlContent));

        $transformationResult->save();
    }

    protected function getTransformationResult(Transformer $transformer): TransformationResult
    {
        $model = Config::model();

        return $model::findOrCreateForRegistration($this->url, $transformer);
    }
}
