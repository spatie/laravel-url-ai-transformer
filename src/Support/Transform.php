<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Closure;

class Transform
{
    public static function urls(string|array|Closure ...$urls): TransformationRegistration
    {
        $flattenedUrls = collect($urls)->flatten()->toArray();

        $registration = new TransformationRegistration($flattenedUrls);

        app(RegisteredTransformations::class)->add($registration);

        return $registration;
    }
}
