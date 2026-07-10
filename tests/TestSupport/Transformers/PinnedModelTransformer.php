<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Laravel\Ai\Attributes\Model;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[Model('gpt-4o')]
class PinnedModelTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the webpage.';
    }
}
