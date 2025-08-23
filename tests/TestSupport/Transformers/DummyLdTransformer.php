<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class DummyLdTransformer extends Transformer
{
    public function transform(): void
    {
        $this->transformationResult->result = 'dummy result';
    }

    public function getPrompt(): string
    {
        return 'dummy prompt';
    }

    public function type(): string
    {
        return 'ld';
    }
}
