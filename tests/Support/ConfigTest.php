<?php

use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Enums\Model;
use Spatie\LaravelUrlAiTransformer\Exceptions\InvalidConfig;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;

it('can get an action class', function () {
    config()->set('url-ai-transformer.actions.test_action', 'stdClass');

    $actionClass = Config::getActionClass('test_action', 'stdClass');

    expect($actionClass)->toBe('stdClass');
});

it('throws an exception when action key does not exist', function () {
    Config::getActionClass('non_existent_action', 'stdClass');
})->throws(InvalidConfig::class);

it('throws an exception when action class does not exist', function () {
    config()->set('url-ai-transformer.actions.test_action', 'NonExistentClass');

    Config::getActionClass('test_action', 'stdClass');
})->throws(InvalidConfig::class);

it('throws an exception when action class does not extend required class', function () {
    config()->set('url-ai-transformer.actions.test_action', 'stdClass');

    Config::getActionClass('test_action', 'DateTime');
})->throws(InvalidConfig::class);

it('can get an action instance', function () {
    config()->set('url-ai-transformer.actions.test_action', 'stdClass');

    $action = Config::getAction('test_action', 'stdClass');

    expect($action)->toBeInstanceOf(stdClass::class);
});

it('can get the model class', function () {
    $modelClass = Config::model();

    expect($modelClass)->toBe(TransformationResult::class);
});

it('throws an exception when model class is not configured', function () {
    config()->set('url-ai-transformer.model', null);

    Config::model();
})->throws(InvalidConfig::class);

it('throws an exception when model class does not exist', function () {
    config()->set('url-ai-transformer.model', 'NonExistentModel');

    Config::model();
})->throws(InvalidConfig::class);

it('can get the AI provider', function () {
    $provider = Config::aiProvider();

    expect($provider)->toBe(Lab::OpenAI);
});

it('can get a custom configured AI provider', function () {
    config()->set('url-ai-transformer.ai.provider', Lab::Anthropic);

    expect(Config::aiProvider())->toBe(Lab::Anthropic);
});

it('throws an exception when AI provider is not configured', function () {
    config()->set('url-ai-transformer.ai.provider', null);

    Config::aiProvider();
})->throws(InvalidConfig::class);

it('throws an exception when AI provider is invalid', function () {
    config()->set('url-ai-transformer.ai.provider', 'invalid_provider');

    Config::aiProvider();
})->throws(InvalidConfig::class);

it('can get the AI model', function () {
    $model = Config::aiModel();

    expect($model)->toBe('gpt-4o-mini');
});

it('can get a custom configured AI model', function () {
    config()->set('url-ai-transformer.ai.model', 'custom-model');

    expect(Config::aiModel())->toBe('custom-model');
});

it('can get a Model enum as the AI model', function () {
    config()->set('url-ai-transformer.ai.model', Model::Smartest);

    expect(Config::aiModel())->toBe(Model::Smartest);
});

it('throws an exception when AI model is not configured', function () {
    config()->set('url-ai-transformer.ai.model', null);

    Config::aiModel();
})->throws(InvalidConfig::class);

it('throws an exception when the AI model is an invalid type', function () {
    config()->set('url-ai-transformer.ai.model', ['not', 'a', 'model']);

    Config::aiModel();
})->throws(InvalidConfig::class);
