<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Support\Str;
use Stringable;

class LdJsonTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the following webpage to ld+json. Only return valid json, no backtick openings. Make the snippet as complete as possible.';
    }

    public function content(): string
    {
        return Str::limit($this->urlContent, 6000);
    }
}
