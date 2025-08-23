<?php

namespace Spatie\LaravelUrlAiTransformer\Exceptions;

use Exception;

class CouldNotFindTransformer extends Exception
{
    public static function make(string $type): static
    {
        return new static("No transformer found for type: {$type}");
    }
}