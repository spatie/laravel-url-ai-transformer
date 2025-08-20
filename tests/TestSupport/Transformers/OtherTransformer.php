<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class OtherTransformer implements Transformer
{
    public function transform(string $url, string $urlContent, TransformationResult $transformationResult,)
    {
        // TODO: Implement transform() method.
    }

    public function getPrompt(string $url, string $urlContent, TransformationResult $transformationResult): string
    {
        // TODO: Implement getPrompt() method.
    }
}
