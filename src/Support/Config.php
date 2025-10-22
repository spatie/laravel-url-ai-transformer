<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Prism\Prism\Enums\Provider;
use Spatie\LaravelUrlAiTransformer\Exceptions\InvalidConfig;

class Config
{
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
