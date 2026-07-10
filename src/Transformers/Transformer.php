<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Stringable;

abstract class Transformer implements Agent
{
    use Promptable;

    public string $url;

    public string $urlContent;

    public ?TransformationResult $transformationResult = null;

    public function instructions(): Stringable|string
    {
        return '';
    }

    public function transform(): void
    {
        $response = $this->prompt(
            prompt: $this->content(),
            provider: Config::aiProvider(),
            model: Config::aiModel(),
        );

        $this->transformationResult->result = $response->text;
    }

    public function content(): string
    {
        return $this->urlContent;
    }

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
            ->classBasename()
            ->beforeLast('Transformer')
            ->lcfirst();
    }

    public function shouldRun(): bool
    {
        return true;
    }
}
