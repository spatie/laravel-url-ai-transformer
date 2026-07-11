<?php

namespace Spatie\LaravelUrlAiTransformer\Exceptions;

use Exception;

class CouldNotFindTransformer extends Exception
{
    public static function make(string $type): self
    {
        return new self("No transformer found for type: {$type}");
    }
}
