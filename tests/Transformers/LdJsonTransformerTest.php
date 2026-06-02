<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Text\Response;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\ValueObjects\Usage;
use Prism\Prism\ValueObjects\Meta;

it('can transform content to ld+json using prism', function () {
    Prism::fake([
        new Response(
            steps: collect(),
            text: '{"@context": "https://schema.org", "@type": "WebPage", "name": "Hello World"}',
            finishReason: FinishReason::Stop,
            toolCalls: [],
            toolResults: [],
            usage: new Usage(10, 20),
            meta: new Meta('1', 'gpt-4'),
            messages: collect(),
        )
    ]);

    $transformer = new LdJsonTransformer();
    $transformer->setTransformationProperties(
        'https://example.com',
        '<html lang=""><body><h1>Hello World</h1></body></html>',
        new TransformationResult()
    );

    $transformer->transform();

    expect($transformer->transformationResult->result)
        ->toBe('{"@context": "https://schema.org", "@type": "WebPage", "name": "Hello World"}');
});
