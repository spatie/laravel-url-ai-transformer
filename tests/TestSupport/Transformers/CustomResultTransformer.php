<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Laravel\Ai\Responses\AgentResponse;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class CustomResultTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the webpage.';
    }

    protected function resultFrom(AgentResponse $response, TransformationResult $transformationResult): string
    {
        // Set extra data on the model that will be saved.
        $transformationResult->url = 'https://example.com/modified';

        return strtoupper($response->text);
    }
}
