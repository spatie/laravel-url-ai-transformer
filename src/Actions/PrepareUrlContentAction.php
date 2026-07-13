<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Str;

class PrepareUrlContentAction
{
    public function execute(string $urlContent): string
    {
        $urlContent = preg_replace('#<(script|style)\b[^>]*>.*?(?:</\1>|$)#is', '', $urlContent) ?? $urlContent;
        $urlContent = preg_replace('#<[^>]+>#', ' ', $urlContent) ?? strip_tags($urlContent);
        $urlContent = html_entity_decode($urlContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(Str::squish($urlContent), 6000);
    }
}
