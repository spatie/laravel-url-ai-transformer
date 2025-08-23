<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class ProcessRegistrationAction
{
    public function execute(
        TransformationRegistration $registration,
        ?string $urlFilter,
        ?string $transformerFilter,
    ): void
    {

        $transformers = $registration->getTransformers();

        if ($transformerFilter) {
            $transformers = $transformers->filter(fn (Transformer $transformer) => fnmatch($transformerFilter, $transformer->type()));
        }

        foreach ($registration->getUrls() as $url) {
            if ($urlFilter && fnmatch($urlFilter, $url) === false) {
                continue;
            }
            $this->processUrl($url, $registration, $transformers);
        }
    }

    protected function processUrl(
        string $url,
        TransformationRegistration $registration,
        Collection $transformers
    ): void {
        try {
            $urlContent = $this->fetchUrlContent($url);
        } catch (Exception $exception) {
            $this->recordExceptionForAllTransformers($url, $transformers, $exception);

            return;
        }

        foreach ($transformers as $transformer) {
            $this->dispatchTransformerJob($transformer, $url, $urlContent);
        }
    }

    protected function fetchUrlContent(string $url): string
    {
        return Http::get($url)->throw()->body();
    }

    protected function dispatchTransformerJob(Transformer $transformer, string $url, string $urlContent): void
    {
        $jobClass = Config::getJobClass('process_transformer_job', ShouldQueue::class);

        $jobClass::dispatch(get_class($transformer), $url, $urlContent);
    }

    protected function getTransformationResult(
        string $url,
        Transformer $transformer,
    ): TransformationResult {
        $model = Config::model();

        return $model::findOrCreateForRegistration($url, $transformer);
    }

    protected function recordExceptionForAllTransformers(
        string $url,
        Collection $transformers,
        Exception $exception,
    ): void {
        foreach ($transformers as $transformer) {
            $transformationResult = $this->getTransformationResult($url, $transformer);
            $transformationResult->recordException($exception);

            event(new TransformerFailed($transformer, $transformationResult, $exception));
        }
    }
}
