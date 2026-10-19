<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\PinnedModelTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\ProductTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

it('stores structured output as JSON when the transformer defines a schema', function () {
    ProductTransformer::fake([
        ['name' => 'Widget', 'sku' => 'W-123'],
    ]);

    $transformer = (new ProductTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult);

    $transformer->transform();

    expect($transformer->transformationResult->result)
        ->toBe('{"name":"Widget","sku":"W-123"}');
});

it('stores responses from the real Laravel AI gateway', function (string $transformerClass, string $responseText, string $expectedResult) {
    config()->set('ai.providers.openai.key', 'test-key');
    config()->set('url-ai-transformer.ai.model', 'gpt-4o-mini');

    Http::preventStrayRequests();
    Http::fake([
        'https://api.openai.com/v1/responses' => Http::response([
            'id' => 'resp_test',
            'model' => 'gpt-4o-mini',
            'status' => 'completed',
            'output' => [[
                'id' => 'msg_test',
                'type' => 'message',
                'role' => 'assistant',
                'content' => [[
                    'type' => 'output_text',
                    'text' => $responseText,
                    'annotations' => [],
                ]],
            ]],
            'usage' => ['input_tokens' => 12, 'output_tokens' => 5],
        ]),
    ]);

    $result = new TransformationResult;

    (new $transformerClass)
        ->setTransformationProperties('https://example.com', '<h1>Widget</h1>', $result)
        ->transform();

    expect($result->result)->toBe($expectedResult);
})->with([
    'text' => [PinnedModelTransformer::class, 'A useful summary.', 'A useful summary.'],
    'structured output' => [ProductTransformer::class, '{"name":"Widget","sku":"W-123"}', '{"name":"Widget","sku":"W-123"}'],
    'ld+json payload' => [LdJsonTransformer::class, '{"json":"{\"@type\":\"Product\",\"name\":\"Widget\"}"}', '{"@type":"Product","name":"Widget"}'],
]);
