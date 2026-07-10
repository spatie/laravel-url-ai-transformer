<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\CustomResultTransformer;

it('lets a transformer customize how the result is stored', function () {
    CustomResultTransformer::fake(['hello world']);

    $transformer = (new CustomResultTransformer)
        ->setTransformationProperties('https://example.com', 'content', new TransformationResult);

    $transformer->transform();

    expect($transformer->transformationResult->result)->toBe('HELLO WORLD');
});
