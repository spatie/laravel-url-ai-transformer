<?php

namespace Spatie\LaravelUrlAiTransformer\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
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
}
