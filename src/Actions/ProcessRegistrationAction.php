<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Exception;
use Illuminate\Support\Collection;
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
        bool $force,
    ): void {

        $transformers = $registration->getTransformers();

        if ($transformerFilter) {
            $transformers = $transformers->filter(fn (Transformer $transformer) => fnmatch($transformerFilter, $transformer->type()));
        }

        foreach ($registration->getUrls() as $url) {
            if ($urlFilter && fnmatch($urlFilter, $url) === false) {
                continue;
            }
            $this->processUrl($url, $registration, $transformers, $force);
        }
    }

    protected function processUrl(
        string $url,
        TransformationRegistration $registration,
        Collection $transformers,
        bool $force,
    ): void {
        try {
            $urlContent = $this->fetchUrlContent($url);
        } catch (Exception $exception) {
            $this->recordExceptionForAllTransformers($url, $transformers, $exception);

            return;
        }

        foreach ($transformers as $transformer) {
            $this->dispatchTransformerJob($transformer, $url, $urlContent, $force);
        }
    }

    protected function fetchUrlContent(string $url): string
    {
        /** @var FetchUrlContentAction $fetchAction */
        $fetchAction = Config::getAction('fetch_url_content', FetchUrlContentAction::class);

        return $fetchAction->execute($url);
    }

    protected function dispatchTransformerJob(Transformer $transformer, string $url, string $urlContent, bool $force): void
    {
        $processTransformationJob = Config::getProcessTransformationJobClass();

        $processTransformationJob::dispatch(get_class($transformer), $url, $urlContent, $force);
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
