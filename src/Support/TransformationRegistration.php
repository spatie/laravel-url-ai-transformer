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

            if (! is_array($url)) {
                $url = [$url];
            }

            foreach ($url as $singleUrl) {
                yield $singleUrl;
            }
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
