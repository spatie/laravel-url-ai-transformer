<?php

namespace Spatie\LaravelUrlAiTransformer\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformerStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Transformer $transformer,
        public TransformationResult $transformationResult,
        public string $url,
        public string $urlContent,
    ) {}
}
