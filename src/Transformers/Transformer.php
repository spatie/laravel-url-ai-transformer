<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Support\Str;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

abstract class Transformer
{
    public string $url;

    public string $urlContent;

    public TransformationResult $transformationResult;

    public function setTransformationProperties(
        string $url,
        string $urlContent,
        TransformationResult $transformationResult,
    ): self {
        $this->url = $url;
        $this->urlContent = $urlContent;
        $this->transformationResult = $transformationResult;

        return $this;
    }

    public function type(): string
    {
        return Str::of(static::class)
            ->basename()
            ->ucfirst()
            ->beforeLast('transformer');
    }

    abstract public function transform(): void;

    abstract public function getPrompt(): string;

    public function shouldRun(): bool
    {
        return true;
    }
}
