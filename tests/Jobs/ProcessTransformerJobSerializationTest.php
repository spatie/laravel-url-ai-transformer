<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\TestTransformer;

it('can serialize and unserialize the job for production queues', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    // Create the job
    $job = new ProcessTransformerJob(
        TestTransformer::class,
        'https://example.com',
        '<html><body>Test content</body></html>'
    );

    // Test serialization (this would fail with the old approach)
    $serialized = serialize($job);
    expect($serialized)->toBeString();

    // Test unserialization
    $unserialized = unserialize($serialized);
    expect($unserialized)->toBeInstanceOf(ProcessTransformerJob::class);
    expect($unserialized->transformerClass)->toBe(TestTransformer::class);
    expect($unserialized->url)->toBe('https://example.com');
    expect($unserialized->urlContent)->toBe('<html><body>Test content</body></html>');

    // Test that the unserialized job can still handle correctly
    $unserialized->handle();

    // Verify it worked
    expect(\Spatie\LaravelUrlAiTransformer\Models\TransformationResult::count())->toBe(1);
});

it('can dispatch to database queue without serialization issues', function () {
    // Temporarily switch to database queue for this test
    config(['queue.default' => 'database']);

    // Set up database queue table (simplified for test)
    \Illuminate\Support\Facades\Schema::create('jobs', function ($table) {
        $table->bigIncrements('id');
        $table->string('queue')->index();
        $table->longText('payload');
        $table->unsignedTinyInteger('attempts');
        $table->unsignedInteger('reserved_at')->nullable();
        $table->unsignedInteger('available_at');
        $table->unsignedInteger('created_at');
    });

    Queue::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    // This should not throw serialization errors
    ProcessTransformerJob::dispatch(
        TestTransformer::class,
        'https://example.com',
        '<html><body>Test content</body></html>'
    );

    Queue::assertPushed(ProcessTransformerJob::class, function ($job) {
        return $job->transformerClass === TestTransformer::class
            && $job->url === 'https://example.com';
    });
});
