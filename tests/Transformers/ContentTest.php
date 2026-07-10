<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\CustomContentTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\PinnedModelTransformer;

it('sends the url content as the prompt by default', function () {
    PinnedModelTransformer::fake(['result']);

    (new PinnedModelTransformer)
        ->setTransformationProperties('https://example.com', 'the page content', new TransformationResult)
        ->transform();

    PinnedModelTransformer::assertPrompted(fn ($prompt) => $prompt->prompt === 'the page content');
});

it('lets a transformer customize the content that is sent to the AI', function () {
    CustomContentTransformer::fake(['result']);

    (new CustomContentTransformer)
        ->setTransformationProperties('https://example.com', 'the page content', new TransformationResult)
        ->transform();

    CustomContentTransformer::assertPrompted(fn ($prompt) => $prompt->prompt === 'custom content for https://example.com');
});
