<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Laravel\Ai\Responses\AgentResponse;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class CustomResultTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the webpage.';
    }

    protected function resultFrom(AgentResponse $response): string
    {
        return strtoupper($response->text);
    }
}
