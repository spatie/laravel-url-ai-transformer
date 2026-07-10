<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Enums\Model;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\CheapestModelTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

it('resolves the smartest model by default', function () {
    LdJsonTransformer::fake(['result']);

    (new LdJsonTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    LdJsonTransformer::assertPrompted(fn ($prompt) => $prompt->model === $prompt->provider->smartestTextModel());
});

it('uses a plain string model from config', function () {
    config()->set('url-ai-transformer.ai.model', 'gpt-4o-mini');

    LdJsonTransformer::fake(['result']);

    (new LdJsonTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    LdJsonTransformer::assertPrompted(fn ($prompt) => $prompt->model === 'gpt-4o-mini');
});

it('resolves a Model enum from config against the configured provider', function () {
    config()->set('url-ai-transformer.ai.model', Model::Cheapest);

    LdJsonTransformer::fake(['result']);

    (new LdJsonTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    LdJsonTransformer::assertPrompted(fn ($prompt) => $prompt->model === $prompt->provider->cheapestTextModel());
});

it('lets a transformer override the model with a Laravel AI attribute', function () {
    CheapestModelTransformer::fake(['result']);

    (new CheapestModelTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    CheapestModelTransformer::assertPrompted(fn ($prompt) => $prompt->model === $prompt->provider->cheapestTextModel());
});
