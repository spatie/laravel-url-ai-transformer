<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Closure;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
            $url = $url instanceof Closure ? ($url)() : $url;

            if (! Str::startsWith($url, 'http')) {
                $url = url($url);
            }

            yield $url;
        }
    }

    /**
     * @return Collection<int, Transformer>
     */
    public function getTransformers(): Collection
    {
        return collect($this->transformers);
    }
}
