<?php

namespace Spatie\LaravelUrlAiTransformer\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function actionKeyNotFound(string $actionName): self
    {
        return new self("There is no action with name `{$actionName}` configured in the `actions` key of the config file.");
    }

    public static function actionClassDoesNotExist(string $actionClass): self
    {
        return new self("Action class '{$actionClass}' does not exist.");
    }

    public static function aiProviderNotConfigured(string $configName): self
    {
        return new self("AI provider not configured for '{$configName}'");
    }

    public static function invalidAiProvider(string $configName): self
    {
        return new self("Invalid AI provider configured for '{$configName}'");
    }

    public static function aiModelNotConfigured(string $configName): self
    {
        return new self("AI model not configured for '{$configName}'");
    }

    public static function actionClassDoesNotExtend(string $actionClass, string $mustBeOrExtend): self
    {
        return new self("Action class '{$actionClass}' must be or extend '{$mustBeOrExtend}'");
    }
}
