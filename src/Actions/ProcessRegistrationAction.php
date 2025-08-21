<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class ProcessRegistrationAction
{
    public function execute(TransformationRegistration $registration)
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
    )
    {
        $transformationResult = $this->getTransformationResult($url, $registration);

        $urlContent = Http::get($url)->throw();

        $transformers
            ->each(fn(Transformer $transformer) => $transformer->setTransformationProperties($url, $urlContent, $transformationResult)
            )
            ->each(fn(Transformer $transformer) => $transformer->transform());

        $transformationResult->save();
    }

    protected function getTransformationResult(
        string $url,
        TransformationRegistration $registration
    ): TransformationResult
    {
        /** @var TransformationResult $model */
        $model = Config::model();

        return $model::findOrCreateForRegistration($url, $registration);
    }
}
