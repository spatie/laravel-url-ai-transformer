<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[Provider(Lab::Anthropic)]
class AnthropicTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the webpage.';
    }
}
