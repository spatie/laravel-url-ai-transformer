<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Collection;
use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Throwable;

class ProcessRegistrationAction
{
    public function execute(
        TransformationRegistration $registration,
        ?string $urlFilter,
        ?string $transformerFilter,
        bool $force,
        bool $now,
    ): int {
        $transformers = $registration->getTransformers();

        if ($transformerFilter) {
            $transformers = $transformers->filter(fn (Transformer $transformer) => fnmatch($transformerFilter, $transformer->type()));
        }

        $dispatchedJobCount = 0;

        foreach ($registration->getUrls() as $url) {
            if ($urlFilter) {
                if (! fnmatch($urlFilter, $url)) {
                    continue;
                }
            }

            $dispatchedJobCount += $this->processUrl($url, $transformers, $force, $now);
        }

        return $dispatchedJobCount;
    }

    /**
     * @param  Collection<int, Transformer>  $transformers
     */
    protected function processUrl(
        string $url,
        Collection $transformers,
        bool $force,
        bool $now,
    ): int {
        try {
            $urlContent = $this->fetchUrlContent($url);
        } catch (Throwable $exception) {
            $this->recordExceptionForAllTransformers($url, $transformers, $exception);

            return 0;
        }

        foreach ($transformers as $transformer) {
            $this->dispatchTransformerJob($transformer, $url, $urlContent, $force, $now);
        }

        return $transformers->count();
    }

    protected function fetchUrlContent(string $url): string
    {
        /** @var FetchUrlContentAction $fetchAction */
        $fetchAction = Config::getAction('fetch_url_content', FetchUrlContentAction::class);

        return $fetchAction->execute($url);
    }

    protected function dispatchTransformerJob(
        Transformer $transformer,
        string $url,
        string $urlContent,
        bool $force,
        bool $now,
    ): void {
        $processTransformationJob = Config::getProcessTransformationJobClass();

        $dispatchMethod = $now
            ? 'dispatchSync'
            : 'dispatch';

        try {
            $processTransformationJob::$dispatchMethod(get_class($transformer), $url, $urlContent, $force);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    protected function getTransformationResult(
        string $url,
        Transformer $transformer,
    ): TransformationResult {
        $model = Config::model();

        return $model::findOrCreateForRegistration($url, $transformer);
    }

    /**
     * @param  Collection<int, Transformer>  $transformers
     */
    protected function recordExceptionForAllTransformers(
        string $url,
        Collection $transformers,
        Throwable $exception,
    ): void {
        foreach ($transformers as $transformer) {
            $transformationResult = $this->getTransformationResult($url, $transformer);
            $transformationResult->recordException($exception);

            event(new TransformerFailed($transformer, $transformationResult, $exception));
        }
    }
}
