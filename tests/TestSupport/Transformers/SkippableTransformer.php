<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class SkippableTransformer extends Transformer
{
    public function transform(): void
    {
        $this->transformationResult->result = 'should not be set';
    }

    public function shouldRun(): bool
    {
        return false;
    }
}
