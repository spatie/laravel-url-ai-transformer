<?php

namespace Spatie\LaravelUrlAiTransformer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\LaravelUrlAiTransformer\LaravelUrlAiTransformer
 */
class LaravelUrlAiTransformer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Spatie\LaravelUrlAiTransformer\LaravelUrlAiTransformer::class;
    }
}
