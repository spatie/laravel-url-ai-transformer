<?php

use Prism\Prism\Enums\Provider;
use Spatie\LaravelUrlAiTransformer\Exceptions\InvalidConfig;
use Spatie\LaravelUrlAiTransformer\Support\Config;

it('can get the AI provider', function () {
    $provider = Config::aiProvider();

    expect($provider)->toBe(Provider::OpenAI);
});

it('can get the AI provider for a specific config', function () {
    config()->set('url-ai-transformer.ai.custom.provider', Provider::Anthropic);

    $provider = Config::aiProvider('custom');

    expect($provider)->toBe(Provider::Anthropic);
});

it('throws an exception when AI provider is not configured', function () {
    config()->set('url-ai-transformer.ai.custom.provider', null);

    Config::aiProvider('custom');
})->throws(InvalidConfig::class);

it('throws an exception when AI provider is invalid', function () {
    config()->set('url-ai-transformer.ai.custom.provider', 'invalid_provider');

    Config::aiProvider('custom');
})->throws(InvalidConfig::class);

it('can get the AI model', function () {
    $model = Config::aiModel();

    expect($model)->toBe('gpt-4o-mini');
});

it('can get the AI model for a specific config', function () {
    config()->set('url-ai-transformer.ai.custom.model', 'custom-model');

    $model = Config::aiModel('custom');

    expect($model)->toBe('custom-model');
});

it('throws an exception when AI model is not configured', function () {
    config()->set('url-ai-transformer.ai.custom.model', null);

    Config::aiModel('custom');
})->throws(InvalidConfig::class);
