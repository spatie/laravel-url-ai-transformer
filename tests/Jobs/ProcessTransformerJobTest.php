<?php

use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\FailingTransformer;

it('rethrows transformer exceptions so the queue can retry the job', function () {
    $job = new ProcessTransformerJob(
        FailingTransformer::class,
        'https://example.com',
        '<h1>Content</h1>',
    );

    expect(fn () => $job->handle())->toThrow(Exception::class);

    $result = TransformationResult::query()->first();

    expect($result)->not->toBeNull();
    expect($result->latest_exception_message)->toBe('Transformer failed to process content');
});
