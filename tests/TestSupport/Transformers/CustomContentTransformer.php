<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class CustomContentTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the webpage.';
    }

    public function content(): string
    {
        return 'custom content for '.$this->url;
    }
}
