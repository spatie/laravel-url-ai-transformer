<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Closure;
use Generator;
use Illuminate\Support\Collection;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformationRegistration
{
    protected array $transformers = [];

    public function __construct(
        protected array $urls,
    )
    {
    }

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

    /**
     * @return Collection<int, Transformer>
     */
    public function getTransformers(): Collection
    {
        return collect($this->transformers);
    }

    public function getType(): string
    {
        return 'default';
    }
}
