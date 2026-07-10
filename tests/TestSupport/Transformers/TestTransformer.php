<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TestTransformer extends Transformer
{
    public function transform(): void
    {
        $this->transformationResult->result = 'test';
    }
}
