<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Exception;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class FailingTransformer extends Transformer
{
    public function transform(): void
    {
        throw new Exception('Transformer failed to process content');
    }

    public function getPrompt(): string
    {
        return 'This prompt will cause failure';
    }
}
