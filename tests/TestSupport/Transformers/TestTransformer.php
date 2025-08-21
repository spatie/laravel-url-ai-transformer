<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TestTransformer extends Transformer
{
    public function transform(): void
    {
        $this->transformationResult->setResult('test', 'test-value');
    }

    public function getPrompt(): string
    {
        return 'Test prompt for ' . $this->url;
    }
}
