<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class OtherTransformer extends Transformer
{
    public function transform(): void
    {
        $this->transformationResult->setResult('other', 'other-value');
    }

    public function getPrompt(): string
    {
        return 'Other prompt for '.$this->url;
    }
}
