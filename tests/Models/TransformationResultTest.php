<?php

use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Models\CustomTransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;

it('returns the result for a matching url and type', function () {
    TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'This is a summary',
    ]);

    $result = TransformationResult::forUrl('https://example.com', 'ld');
    expect($result)->toBe('This is a summary');

    $result = TransformationResult::forUrl('https://other.com', 'ld');
    expect($result)->toBeNull();

    $result = TransformationResult::forUrl('https://example.com', 'other');
    expect($result)->toBeNull();
});

it('returns an empty string when result field is empty', function () {
    TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'summary',
        'result' => '',
    ]);

    $result = TransformationResult::forUrl('https://example.com', 'summary');

    expect($result)->toBe('');
});

it('can retrieve a result using the transformer class name', function () {
    TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'This is a summary',
    ]);

    $result = TransformationResult::forUrl('https://example.com', DummyLdTransformer::class);

    expect($result)->toBe('This is a summary');
});

it('can find the underlying model for a url and type', function () {
    TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'This is a summary',
    ]);

    $transformationResult = TransformationResult::findForUrl('https://example.com', 'ld');

    expect($transformationResult)->toBeInstanceOf(TransformationResult::class);
    expect($transformationResult->result)->toBe('This is a summary');

    expect(TransformationResult::findForUrl('https://other.com', 'ld'))->toBeNull();
});

it('returns instances of a custom model', function () {
    $transformationResult = CustomTransformationResult::findOrCreateForRegistration(
        'https://example.com',
        new DummyLdTransformer,
    );

    expect($transformationResult)->toBeInstanceOf(CustomTransformationResult::class);
});

it('uses the custom model configured in the config file', function () {
    config()->set('url-ai-transformer.model', CustomTransformationResult::class);

    Http::fake([
        'https://example.com' => Http::response('<html><body>Content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $this->artisan('transform-urls')->assertSuccessful();

    expect(CustomTransformationResult::forUrl('https://example.com', 'ld'))->toBe('dummy result');
});
