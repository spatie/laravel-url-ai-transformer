<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Closure;
use Generator;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformationRegistration
{
    protected array $transformers = [];

    public function __construct(
        protected array $urls,
    ) {}

    public function usingTransformers(Transformer ...$transformers): self
    {
        $this->transformers = $transformers;

        return $this;
    }

    public function getUrls(): Generator
    {
        foreach ($this->urls as $url) {
            yield $url instanceof Closure ? ($url)() : $url;
        }
    }

    public function getTransformers(): array
    {
        return $this->transformers;
    }
}
