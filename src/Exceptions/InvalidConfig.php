<?php

namespace Spatie\LaravelUrlAiTransformer\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function actionKeyNotFound(string $actionName): static
    {
        return new static("There is no action with name `{$actionName}` configured in the `actions` key of the config file.");
    }

    public static function actionClassDoesNotExist(string $actionClass): static
    {
        return new static("Action class '{$actionClass}' does not exist.");
    }

    public static function modelClassNotConfigured(): static
    {
        return new static('Model class not configured. Please set the `model` key in the config file to the fully qualified class name of your model.');
    }

    public static function modelClassDoesNotExist(string $modelClass): static
    {
        return new static("Model class '{$modelClass}' does not exist");
    }

    public static function aiProviderNotConfigured(string $configName): static
    {
        return new static("AI provider not configured for '{$configName}'");
    }

    public static function invalidAiProvider(string $configName): static
    {
        return new static("Invalid AI provider configured for '{$configName}'");
    }

    public static function aiModelNotConfigured(string $configName): static
    {
        return new static("AI model not configured for '{$configName}'");
    }

    public static function actionClassDoesNotExtend(string $actionClass, string $mustBeOrExtend): static
    {
        return new static("Action class '{$actionClass}' must be or extend '{$mustBeOrExtend}'");
    }
}
