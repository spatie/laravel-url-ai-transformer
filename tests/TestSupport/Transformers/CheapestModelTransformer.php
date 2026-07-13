<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers;

use Laravel\Ai\Attributes\UseCheapestModel;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[UseCheapestModel]
class CheapestModelTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the webpage.';
    }
}
