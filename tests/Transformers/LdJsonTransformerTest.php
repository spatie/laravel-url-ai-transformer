<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use JsonException;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

it('can transform content to ld+json', function () {
    LdJsonTransformer::fake([
        ['json' => '{"@context": "https://schema.org", "@type": "WebPage", "name": "Hello World"}'],
    ]);

    $transformer = new LdJsonTransformer;
    $transformer->setTransformationProperties(
        'https://example.com',
        '<html lang=""><body><h1>Hello World</h1></body></html>',
        new TransformationResult
    );

    $transformer->transform();

    expect($transformer->transformationResult->result)
        ->toBe('{"@context": "https://schema.org", "@type": "WebPage", "name": "Hello World"}');
});

it('rejects invalid ld+json returned by the AI', function () {
    LdJsonTransformer::fake([
        ['json' => 'not valid JSON'],
    ]);

    $transformer = (new LdJsonTransformer)->setTransformationProperties(
        'https://example.com',
        '<h1>Hello World</h1>',
        new TransformationResult,
    );

    expect(fn () => $transformer->transform())->toThrow(JsonException::class);
});
