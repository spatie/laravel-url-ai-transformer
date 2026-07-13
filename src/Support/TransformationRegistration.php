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

    /**
     * @param  Transformer|class-string<Transformer>  ...$transformers
     */
    public function usingTransformers(Transformer|string ...$transformers): self
    {
        $this->transformers = array_map(
            fn (Transformer|string $transformer) => $this->resolveTransformer($transformer),
            $transformers,
        );

        return $this;
    }

    /**
     * @param  Transformer|class-string<Transformer>  $transformer
     */
    protected function resolveTransformer(Transformer|string $transformer): Transformer
    {
        if ($transformer instanceof Transformer) {
            return $transformer;
        }

        return app($transformer);
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
