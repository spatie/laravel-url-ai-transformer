<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Prism\Prism\Enums\Provider;
use Spatie\LaravelUrlAiTransformer\Exceptions\InvalidConfig;

class Config
{
    /**
     * @template T
     * @param string $actionKey
     * @param class-string<T> $mustBeOrExtend
     *
     * @return class-string<T>
     */
    public static function getActionClass(string $actionKey, string $mustBeOrExtend): string
    {
        $actionClass = config("url-ai-transformer.actions.{$actionKey}");

        if (! $actionClass) {
            throw InvalidConfig::actionKeyNotFound($actionKey);
        }

        if (! class_exists($actionClass)) {
            throw InvalidConfig::actionClassDoesNotExist($actionClass);
        }

        if (! is_a($actionClass, $mustBeOrExtend, true)) {
            throw InvalidConfig::actionClassDoesNotExtend($actionClass, $mustBeOrExtend);
        }

        return $actionClass;
    }

    /**
     * @template T
     * @param string $actionKey
     * @param class-string<T> $mustBeOrExtend
     *
     * @return T
     */
    public static function getAction(string $actionKey, string $mustBeOrExtend): object
    {
        $actionClass = self::getActionClass($actionKey, $mustBeOrExtend);

        return app($actionClass);
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
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
