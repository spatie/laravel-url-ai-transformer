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

    public static function modelClassNotConfigured(): self
    {
        return new self('Model class not configured. Please set the `model` key in the config file to the fully qualified class name of your model.');
    }

    public static function modelClassDoesNotExist(string $modelClass): self
    {
        return new self("Model class '{$modelClass}' does not exist");
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

    public static function jobKeyNotFound(string $jobName): self
    {
        return new self("There is no job with name `{$jobName}` configured in the `jobs` key of the config file.");
    }

    public static function jobClassDoesNotExist(string $jobClass): self
    {
        return new self("Job class '{$jobClass}' does not exist.");
    }

    public static function jobClassDoesNotExtend(string $jobClass, string $mustBeOrExtend): self
    {
        return new self("Job class '{$jobClass}' must be or extend '{$mustBeOrExtend}'");
    }
}
