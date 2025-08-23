<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Events\TransformerEnded;
use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;
use Spatie\LaravelUrlAiTransformer\Events\TransformerStarted;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class ProcessRegistrationAction
{
    public function execute(TransformationRegistration $registration): void
    {
        $transformers = $registration->getTransformers();

        foreach ($registration->getUrls() as $url) {
            $this->processUrl($url, $registration, $transformers);
        }
    }

    protected function processUrl(
        string $url,
        TransformationRegistration $registration,
        Collection $transformers
    ): void
    {
        try {
            $urlContent = $this->fetchUrlContent($url);
        } catch (Exception $exception) {
            $this->recordExceptionForAllTransformers($url, $transformers, $exception);
            return;
        }

        foreach ($transformers as $transformer) {
            try {
                $this->processTransformer($transformer, $url, $urlContent);
            } catch (Exception $exception) {
                $transformationResult = $this->getTransformationResult($url, $transformer);
                $transformationResult->recordException($exception);

                event(new TransformerFailed($transformer, $transformationResult, $exception));
            }
        }
    }

    protected function fetchUrlContent(string $url): string
    {
        return Http::get($url)->throw()->body();
    }

    protected function processTransformer(Transformer $transformer, string $url, string $urlContent): void
    {
        $transformationResult = $this->getTransformationResult($url, $transformer);

        $transformer->setTransformationProperties($url, $urlContent, $transformationResult);

        if (!$transformer->shouldRun()) {
            return;
        }

        event(new TransformerStarted($transformer, $transformationResult, $url, $urlContent));

        $transformer->transform();

        event(new TransformerEnded($transformer, $transformationResult, $url, $urlContent));

        $transformationResult->save();
    }

    protected function getTransformationResult(
        string $url,
        Transformer $transformer,
    ): TransformationResult
    {
        $model = Config::model();

        return $model::findOrCreateForRegistration($url, $transformer);
    }

    protected function recordExceptionForAllTransformers(
        string $url,
        Collection $transformers,
        Exception $exception,
    ): void
    {
        foreach ($transformers as $transformer) {
            $transformationResult = $this->getTransformationResult($url, $transformer);
            $transformationResult->recordException($exception);

            event(new TransformerFailed($transformer, $transformationResult, $exception));
        }
    }
}
