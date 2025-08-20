<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

class LdJsonTransformer implements Transformer
{
    public function transform(
        string $url,
        string $urlContent,
        TransformationResult $transformationResult,
    ) {
        Prism::text()
            ->using(Provider::Anthropic, 'claude-3-5-sonnet-20240620');
    }

    public function getPrompt(
        string $url,
        string $urlContent,
        TransformationResult $transformationResult
    ): string {
        return 'Summarize the following webpage to ld+json. '.$urlContent;
    }
}
