<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;

class ProcessRegistrationAction
{
    public function execute(TransformationRegistration $registration)
    {
        $transformers = $registration->getTransformers();

        foreach ($registration->getUrls() as $url) {
            $this->processUrl($url, $registration, $transformers);
        }
    }

    /**
     * @param  array<int, \Spatie\LaravelUrlAiTransformer\Transformers\Transformer>  $transformers
     * @return void
     */
    protected function processUrl(
        string $url,
        TransformationRegistration $registration,
        array $transformers
    ) {
        $transformationResult = $this->getTransformationResult($url, $registration);

        foreach ($transformers as $transformer) {
            $transformer->transform($url, $urlContent, $transformationResult);
        }
    }

    protected function getTransformationResult(string $url, TransformationRegistration $registration): TransformationResult
    {
        /** @var TransformationResult $model */
        $model = config('laravel-url-ai-transformer.model');

        return $model::findOrCreateForRegistration($url, $registration);
    }
}
