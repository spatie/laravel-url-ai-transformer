<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

interface Transformer
{
    public function transform(
        string $url,
        string $urlContent,
        TransformationResult $transformationResult,
    );

    public function getPrompt(
        string $url,
        string $urlContent,
        TransformationResult $transformationResult
    ): string;
}
