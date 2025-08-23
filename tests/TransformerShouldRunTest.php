<?php

use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\SkippableTransformer;

it('runs transformers when shouldRun returns true', function () {
    Transform::urls('https://spatie.be')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    expect(TransformationResult::forUrl('https://spatie.be', 'ld'))->toBe('dummy result');
});

it('skips transformers when shouldRun returns false', function () {
    Transform::urls('https://spatie.be')->usingTransformers(new SkippableTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    expect(TransformationResult::forUrl('https://spatie.be', 'skippable'))->toBeNull();
});

it('can run multiple transformers with different shouldRun values', function () {
    Transform::urls('https://spatie.be')->usingTransformers(
        new DummyLdTransformer,
        new SkippableTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    expect(TransformationResult::forUrl('https://spatie.be', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://spatie.be', 'skippable'))->toBeNull();
});
