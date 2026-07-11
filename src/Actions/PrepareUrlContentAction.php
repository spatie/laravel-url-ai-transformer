<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Str;

class PrepareUrlContentAction
{
    public function execute(string $urlContent): string
    {
        $withoutScriptsAndStyles = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $urlContent);

        return Str::limit(Str::squish(strip_tags($withoutScriptsAndStyles)), 6000);
    }
}
