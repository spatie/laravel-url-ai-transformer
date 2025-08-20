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
}
