<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\FailingTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\TestTransformer;

it('saves exception details when HTTP request fails', function () {
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
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
    expect($result->latest_exception_seen_at)->not->toBeNull();
    expect($result->latest_exception_message)->toContain('500');
    expect($result->latest_exception_trace)->not->toBeNull();
    expect($result->result)->toBeNull();
});

it('saves exception for all transformers when URL fetch fails', function () {
    Http::fake([
        'https://example.com' => Http::response('Not Found', 404),
    ]);

    Transform::urls('https://example.com')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    // Debug: check what types are actually stored
    $allResults = TransformationResult::query()
        ->where('url', 'https://example.com')
        ->get();

    // Check for both transformers
    expect($allResults)->toHaveCount(2);

    $ldResult = $allResults->firstWhere('type', 'ld');
    $testResult = $allResults->firstWhere('type', 'Test');

    expect($ldResult)->not->toBeNull();
    expect($ldResult->latest_exception_seen_at)->not->toBeNull();
    expect($ldResult->latest_exception_message)->toContain('404');

    expect($testResult)->not->toBeNull();
    expect($testResult->latest_exception_seen_at)->not->toBeNull();
    expect($testResult->latest_exception_message)->toContain('404');
});

it('processes successful URLs normally when no exception occurs', function () {
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
    expect($result->latest_exception_seen_at)->toBeNull();
    expect($result->latest_exception_message)->toBeNull();
    expect($result->latest_exception_trace)->toBeNull();
    expect($result->result)->toBe('dummy result');
});

it('handles connection timeout exceptions', function () {
    Http::fake(function () {
        throw new RequestException(new \GuzzleHttp\Psr7\Response(0, [], 'Connection timeout'));
    });

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    $result = TransformationResult::query()
        ->where('url', 'https://example.com')
        ->where('type', 'ld')
        ->first();

    expect($result)->not->toBeNull();
    expect($result->latest_exception_seen_at)->not->toBeNull();
    expect($result->latest_exception_message)->not->toBeNull();
    expect($result->latest_exception_trace)->not->toBeNull();
});

it('only records exception for failing transformer when individual transformer fails', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Success</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(
        new DummyLdTransformer,
        new FailingTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    $ldResult = TransformationResult::query()
        ->where('url', 'https://example.com')
        ->where('type', 'ld')
        ->first();

    $failingResult = TransformationResult::query()
        ->where('url', 'https://example.com')
        ->where('type', 'Failing')
        ->first();

    // Successful transformer should have no exception
    expect($ldResult)->not->toBeNull();
    expect($ldResult->latest_exception_seen_at)->toBeNull();
    expect($ldResult->latest_exception_message)->toBeNull();
    expect($ldResult->latest_exception_trace)->toBeNull();
    expect($ldResult->result)->toBe('dummy result');

    // Failing transformer should have exception recorded
    expect($failingResult)->not->toBeNull();
    expect($failingResult->latest_exception_seen_at)->not->toBeNull();
    expect($failingResult->latest_exception_message)->toBe('Transformer failed to process content');
    expect($failingResult->latest_exception_trace)->not->toBeNull();
    expect($failingResult->result)->toBeNull();
});

it('clears exception data when transformation succeeds after previously failing', function () {
    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'latest_exception_seen_at' => now(),
        'latest_exception_message' => 'Previous error',
        'latest_exception_trace' => 'Previous trace',
    ]);

    expect($transformationResult->latest_exception_seen_at)->not->toBeNull();
    expect($transformationResult->latest_exception_message)->toBe('Previous error');
    expect($transformationResult->latest_exception_trace)->toBe('Previous trace');

    Http::fake([
        'https://example.com' => Http::response('<html><body>Success</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    $transformationResult->refresh();

    // Exception data should be cleared after successful transformation
    expect($transformationResult->latest_exception_seen_at)->toBeNull();
    expect($transformationResult->latest_exception_message)->toBeNull();
    expect($transformationResult->latest_exception_trace)->toBeNull();

    // Result should be set and successfully_completed_at should be updated
    expect($transformationResult->result)->toBe('dummy result');
    expect($transformationResult->successfully_completed_at)->not->toBeNull();
});
