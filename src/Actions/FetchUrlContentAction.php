<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Facades\Http;

class FetchUrlContentAction
{
    public function execute(string $url): string
    {
        if (! str_starts_with($url, 'http')) {
            $url = url($url);
        }

        return Http::get($url)->throw()->body();
    }
}
