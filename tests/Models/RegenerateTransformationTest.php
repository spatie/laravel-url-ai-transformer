<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;

it('can regenerate a transformation', function () {
    Queue::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Updated content</body></html>', 200),
    ]);

    // Create a transformation result
    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'old result',
    ]);

    // Regenerate the transformation
    $transformationResult->regenerate(DummyLdTransformer::class);

    // Assert the job was dispatched with correct parameters
    Queue::assertPushed(ProcessTransformerJob::class, function ($job) {
        return $job->transformerClass === DummyLdTransformer::class
            && $job->url === 'https://example.com'
            && str_contains($job->urlContent, 'Updated content');
    });
});

it('fetches fresh content when regenerating', function () {
    Queue::fake();

    $responses = [
        'https://example.com' => Http::sequence()
            ->push('<html><body>Original content</body></html>', 200)
            ->push('<html><body>Fresh content</body></html>', 200),
    ];

    Http::fake($responses);

    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'Test',
        'result' => 'existing result',
    ]);

    // First request would have been during original processing
    Http::get('https://example.com');

    // Regenerate should fetch fresh content
    $transformationResult->regenerate('SomeTransformer');

    Queue::assertPushed(ProcessTransformerJob::class, function ($job) {
        return str_contains($job->urlContent, 'Fresh content');
    });
});

it('throws exception when URL fetch fails during regeneration', function () {
    Queue::fake();

    Http::fake([
        'https://example.com' => Http::response('Not Found', 404),
    ]);

    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'old result',
    ]);

    // This should throw an exception
    expect(fn () => $transformationResult->regenerate(DummyLdTransformer::class))
        ->toThrow(\Illuminate\Http\Client\RequestException::class);

    // Job should not be dispatched when URL fetch fails
    Queue::assertNotPushed(ProcessTransformerJob::class);
});

it('can regenerate with a transformer instance', function () {
    Queue::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Content</body></html>', 200),
    ]);

    $transformationResult = TransformationResult::create([
        'url' => 'https://example.com',
        'type' => 'ld',
        'result' => 'old result',
    ]);

    $transformer = new DummyLdTransformer;

    // Regenerate using transformer instance
    $transformationResult->regenerateWithTransformer($transformer);

    // Assert the job was dispatched with correct transformer class
    Queue::assertPushed(ProcessTransformerJob::class, function ($job) {
        return $job->transformerClass === DummyLdTransformer::class
            && $job->url === 'https://example.com';
    });
});
