<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\CheapestModelTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

it('prompts with the configured model by default', function () {
    LdJsonTransformer::fake(['result']);

    (new LdJsonTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    LdJsonTransformer::assertPrompted(fn ($prompt) => $prompt->model === 'gpt-4o-mini');
});

it('lets a transformer override the model with a Laravel AI attribute', function () {
    CheapestModelTransformer::fake(['result']);

    (new CheapestModelTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    CheapestModelTransformer::assertPrompted(fn ($prompt) => $prompt->model === $prompt->provider->cheapestTextModel()
        && $prompt->model !== 'gpt-4o-mini');
});
