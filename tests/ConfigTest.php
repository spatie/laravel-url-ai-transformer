<?php

use Prism\Prism\Enums\Provider;
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