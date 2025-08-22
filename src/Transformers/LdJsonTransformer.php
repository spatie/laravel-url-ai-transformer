<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Prism\Prism\Prism;
use Spatie\LaravelUrlAiTransformer\Support\Config;

class LdJsonTransformer extends Transformer
{
    public function transform(): void {
        $response = Prism::text()
            ->using(Config::aiProvider(), Config::aiModel())
            ->withPrompt($this->getPrompt())
            ->asText();

        $this->transformationResult->result = $response->text;
    }

    public function getPrompt(): string {
        return 'Summarize the following webpage to ld+json. Only return valid json, no backtick openings. This is the content:' . $this->urlContent;
    }
}
