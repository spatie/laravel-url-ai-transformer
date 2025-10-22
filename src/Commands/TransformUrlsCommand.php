<?php

namespace Spatie\LaravelUrlAiTransformer\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformUrlsCommand extends Command
{
    protected $signature = 'transform-urls
            {--url : Filter transformations by URL (supports * wildcard)}
            {--transformer : Filter transformations by transformer type (supports * wildcard)}
            {--force : Force the operation even when transformer shouldRun returns false}
            {--now : Dispatch jobs immediately, instead of using a queued job}
    ';

    public function handle(): void
    {
        $this->info('Dispatching jobs to transform URLs...');

        $urlFilter = $this->option('url');
        $transformerFilter = $this->option('transformer');
        $force = (bool) $this->option('force');
        $now = (bool) $this->option('now');

        app(RegisteredTransformations::class)
            ->all()
            ->each(fn (TransformationRegistration $registration) => $this->processRegistration($registration, $urlFilter, $transformerFilter, $force, $now)
            );

        $this->info('All done!');
    }

    protected function processRegistration(
        TransformationRegistration $registration,
        ?string $urlFilter,
        ?string $transformerFilter,
        bool $force,
        bool $now,
    ): void {
        $transformers = $registration->getTransformers();

        if ($transformerFilter) {
            $transformers = $transformers->filter(fn (Transformer $transformer) => fnmatch($transformerFilter, $transformer->type()));
        }

        foreach ($registration->getUrls() as $url) {
            if ($urlFilter && fnmatch($urlFilter, $url) === false) {
                continue;
            }
            $this->processUrl($url, $transformers, $force, $now);
        }
    }

    protected function processUrl(
        string $url,
        Collection $transformers,
        bool $force,
        bool $now,
    ): void {
        try {
            $urlContent = $this->fetchUrlContent($url);
        } catch (Exception $exception) {
            $this->recordExceptionForAllTransformers($url, $transformers, $exception);

            return;
        }

        foreach ($transformers as $transformer) {
            $this->dispatchTransformerJob($transformer, $url, $urlContent, $force, $now);
        }
    }

    protected function fetchUrlContent(string $url): string
    {
        if (! str_starts_with($url, 'http')) {
            $url = url($url);
        }

        return Http::get($url)->throw()->body();
    }

    protected function dispatchTransformerJob(
        Transformer $transformer,
        string $url,
        string $urlContent,
        bool $force,
        bool $now,
    ): void {
        $dispatchMethod = $now
            ? 'dispatchSync'
            : 'dispatch';

        try {
            ProcessTransformerJob::$dispatchMethod(get_class($transformer), $url, $urlContent, $force);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    protected function getTransformationResult(
        string $url,
        Transformer $transformer,
    ): TransformationResult {
        return TransformationResult::findOrCreateForRegistration($url, $transformer);
    }

    protected function recordExceptionForAllTransformers(
        string $url,
        Collection $transformers,
        Exception $exception,
    ): void {
        foreach ($transformers as $transformer) {
            $transformationResult = $this->getTransformationResult($url, $transformer);
            $transformationResult->recordException($exception);
        }
    }
}
