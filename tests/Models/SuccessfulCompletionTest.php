<?php

use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\FailingTransformer;

it('sets successfully_completed_at when transformation succeeds', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Success</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    $result = TransformationResult::query()
        ->where('url', 'https://example.com')
        ->where('type', 'ld')
        ->first();

    expect($result)->not->toBeNull();
    expect($result->successfully_completed_at)->not->toBeNull();
    expect($result->successfully_completed_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($result->result)->toBe('dummy result');
    expect($result->latest_exception_seen_at)->toBeNull();
});

it('does not set successfully_completed_at when transformation fails', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new FailingTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    $result = TransformationResult::query()
        ->where('url', 'https://example.com')
        ->where('type', 'Failing')
        ->first();

    expect($result)->not->toBeNull();
    expect($result->successfully_completed_at)->toBeNull();
    expect($result->result)->toBeNull();
    expect($result->latest_exception_seen_at)->not->toBeNull();
    expect($result->latest_exception_message)->toBe('Transformer failed to process content');
});
