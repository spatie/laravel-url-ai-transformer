<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use InvalidArgumentException;

class Config
{
    public static function getActionClass(string $actionKey): string
    {
        $actionClass = config("url-ai-transformer.actions.{$actionKey}");

        if (! $actionClass) {
            throw new InvalidArgumentException("Action '{$actionKey}' not found in config");
        }

        if (! class_exists($actionClass)) {
            throw new InvalidArgumentException("Action class '{$actionClass}' does not exist");
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
            throw new InvalidArgumentException('Model class not configured');
        }
        
        if (! class_exists($modelClass)) {
            throw new InvalidArgumentException("Model class '{$modelClass}' does not exist");
        }
        
        return $modelClass;
    }
    
    public static function aiProvider(string $configName = 'default'): \Prism\Prism\Enums\Provider
    {
        $provider = config("url-ai-transformer.ai.{$configName}.provider");
        
        if (! $provider) {
            throw new InvalidArgumentException("AI provider not configured for '{$configName}'");
        }
        
        if (! $provider instanceof \Prism\Prism\Enums\Provider) {
            throw new InvalidArgumentException("Invalid AI provider configured for '{$configName}'");
        }
        
        return $provider;
    }
    
    public static function aiModel(string $configName = 'default'): string
    {
        $model = config("url-ai-transformer.ai.{$configName}.model");
        
        if (! $model) {
            throw new InvalidArgumentException("AI model not configured for '{$configName}'");
        }
        
        return $model;
    }
}
