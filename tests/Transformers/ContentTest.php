<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\Transformers;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Actions\UppercasePrepareUrlContentAction;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\CustomContentTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\PinnedModelTransformer;

it('sends the url content as the prompt by default', function () {
    PinnedModelTransformer::fake(['result']);

    (new PinnedModelTransformer)
        ->setTransformationProperties('https://example.com', 'the page content', new TransformationResult)
        ->transform();

    PinnedModelTransformer::assertPrompted(fn ($prompt) => $prompt->prompt === 'the page content');
});

it('prepares the url content before sending it to the AI', function () {
    PinnedModelTransformer::fake(['result']);

    $urlContent = '<html><head><script>var ignored = true;</script><style>p { color: red; }</style></head><body><h1>Hello</h1><p>world &amp; friends</p></body></html>';

    (new PinnedModelTransformer)
        ->setTransformationProperties('https://example.com', $urlContent, new TransformationResult)
        ->transform();

    PinnedModelTransformer::assertPrompted(fn ($prompt) => $prompt->prompt === 'Hello world & friends');
});

it('can use a custom prepare url content action', function () {
    config()->set('url-ai-transformer.actions.prepare_url_content', UppercasePrepareUrlContentAction::class);

    PinnedModelTransformer::fake(['result']);

    (new PinnedModelTransformer)
        ->setTransformationProperties('https://example.com', 'the page content', new TransformationResult)
        ->transform();

    PinnedModelTransformer::assertPrompted(fn ($prompt) => $prompt->prompt === 'THE PAGE CONTENT');
});

it('lets a transformer customize the content that is sent to the AI', function () {
    CustomContentTransformer::fake(['result']);

    (new CustomContentTransformer)
        ->setTransformationProperties('https://example.com', 'the page content', new TransformationResult)
        ->transform();

    CustomContentTransformer::assertPrompted(fn ($prompt) => $prompt->prompt === 'custom content for https://example.com');
});
