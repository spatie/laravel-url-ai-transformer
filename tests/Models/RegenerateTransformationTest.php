<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\LaravelUrlAiTransformer\Exceptions\CouldNotFindTransformer;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;

it('can regenerate a transformation using queue', function () {
    Queue::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Updated content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'old result',
    ]);

    $transformationResult->regenerate();

    Queue::assertPushed(ProcessTransformerJob::class, function ($job) {
        return $job->transformerClass === DummyLdTransformer::class
            && $job->url === 'https://example.com'
            && str_contains($job->urlContent, 'Updated content');
    });
});

it('can regenerate a transformation immediately', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Fresh content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'old result',
        'successfully_completed_at' => null,
    ]);

    $transformationResult->regenerateNow();

    $transformationResult->refresh();
    expect($transformationResult->result)->toBe('dummy result');
    expect($transformationResult->successfully_completed_at)->not->toBeNull();
});

it('throws exception when no transformer is found for type', function () {
    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'nonexistent',
        'result' => 'old result',
    ]);

    expect(fn () => $transformationResult->regenerate())
        ->toThrow(CouldNotFindTransformer::class, 'No transformer found for type: nonexistent');
});
