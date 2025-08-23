<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Facades\Http;

class FetchUrlContentAction
{
    public function execute(string $url): string
    {
        return Http::get($url)->throw()->body();
    }
}
