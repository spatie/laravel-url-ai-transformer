<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\ProductTransformer;

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
