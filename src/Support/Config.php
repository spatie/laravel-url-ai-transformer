<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Prism\Prism\Enums\Provider;
use Spatie\LaravelUrlAiTransformer\Exceptions\InvalidConfig;

class Config
{
    public static function getActionClass(string $actionKey): string
    {
        $actionClass = config("url-ai-transformer.actions.{$actionKey}");

        if (! $actionClass) {
            throw InvalidConfig::actionKeyNotFound($actionKey);
        }

        if (! class_exists($actionClass)) {
            throw InvalidConfig::actionClassDoesNotExist($actionClass);
        }

        return $actionClass;
    }

    public static function getAction(string $actionKey): object
    {
        $actionClass = self::getActionClass($actionKey);

        return app($actionClass);
    }

    public static function model(): string
    {
        $modelClass = config('url-ai-transformer.model');

        if (! $modelClass) {
            throw InvalidConfig::modelClassNotConfigured();
        }

        if (! class_exists($modelClass)) {
            throw InvalidConfig::modelClassDoesNotExist($modelClass);
        }

        return $modelClass;
    }

    public static function aiProvider(string $configName = 'default'): Provider
    {
        $provider = config("url-ai-transformer.ai.{$configName}.provider");

        if (! $provider) {
            throw InvalidConfig::aiProviderNotConfigured($configName);
        }

        if (! $provider instanceof Provider) {
            throw InvalidConfig::invalidAiProvider($configName);
        }

        return $provider;
    }

    public static function aiModel(string $configName = 'default'): string
    {
        $model = config("url-ai-transformer.ai.{$configName}.model");

        if (! $model) {
            throw InvalidConfig::aiModelNotConfigured($configName);
        }

        return $model;
    }
}
