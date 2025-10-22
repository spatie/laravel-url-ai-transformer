<?php

namespace Spatie\LaravelUrlAiTransformer\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
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
        public string $urlContent,
        public bool $force = false,
    ) {}

    public function handle(): void
    {
        $transformer = new $this->transformerClass;

        try {
            $this->processTransformer($transformer);
        } catch (Exception $exception) {
            $transformationResult = $this->getTransformationResult($transformer);

            $transformationResult->recordException($exception);

            report($exception);
        }
    }

    protected function processTransformer(Transformer $transformer): void
    {
        $transformationResult = $this->getTransformationResult($transformer);

        $transformer->setTransformationProperties($this->url, $this->urlContent, $transformationResult);

        if (! $this->force) {
            if (! $transformer->shouldRun()) {
                return;
            }
        }

        $transformer->transform();

        $transformationResult->successfully_completed_at = now();

        $transformationResult->clearException(persist: false);

        $transformationResult->save();
    }

    protected function getTransformationResult(Transformer $transformer): TransformationResult
    {
        return TransformationResult::findOrCreateForRegistration($this->url, $transformer);
    }
}
