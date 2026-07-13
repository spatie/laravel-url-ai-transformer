<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Enums\Model;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\AnthropicTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\CheapestModelTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\PinnedModelTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

it('resolves the smartest model by default', function () {
    LdJsonTransformer::fake([['json' => 'result']]);

    (new LdJsonTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    LdJsonTransformer::assertPrompted(fn ($prompt) => $prompt->model === $prompt->provider->smartestTextModel());
});

it('uses a plain string model from config', function () {
    config()->set('url-ai-transformer.ai.model', 'gpt-4o-mini');

    LdJsonTransformer::fake([['json' => 'result']]);

    (new LdJsonTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    LdJsonTransformer::assertPrompted(fn ($prompt) => $prompt->model === 'gpt-4o-mini');
});

it('resolves a Model enum from config against the configured provider', function () {
    config()->set('url-ai-transformer.ai.model', Model::Cheapest);

    LdJsonTransformer::fake([['json' => 'result']]);

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

it('uses the provider from a #[Provider] attribute', function () {
    AnthropicTransformer::fake(['result']);

    (new AnthropicTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    AnthropicTransformer::assertPrompted(fn ($prompt) => $prompt->provider->name() === 'anthropic'
        && $prompt->model === $prompt->provider->defaultTextModel());
});

it('uses the model from a #[Model] attribute on the configured provider', function () {
    PinnedModelTransformer::fake(['result']);

    (new PinnedModelTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult)
        ->transform();

    PinnedModelTransformer::assertPrompted(fn ($prompt) => $prompt->provider->name() === 'openai'
        && $prompt->model === 'gpt-4o');
});
