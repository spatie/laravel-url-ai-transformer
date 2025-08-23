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
    ) {
        $urlContent = Http::get($url)->throw();

        foreach ($transformers as $transformer) {
            $this->processTransformer($transformer, $url, $urlContent);
        }
    }

    public function processTransformer(Transformer $transformer, string $url, string $urlContent): void
    {
        $transformationResult = $this->getTransformationResult($url, $transformer);

        $transformer->setTransformationProperties($url, $urlContent, $transformationResult);

        $transformer->transform();

        $transformationResult->save();
    }

    protected function getTransformationResult(
        string $url,
        Transformer $transformer
    ): TransformationResult {
        /** @var TransformationResult $model */
        $model = Config::model();

        return $model::findOrCreateForRegistration($url, $transformer);
    }
}
