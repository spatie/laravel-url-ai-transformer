<?php

use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\SkippableTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\TestTransformer;

it('returns true by default for shouldRun', function () {
    $transformer = new DummyLdTransformer;

    expect($transformer->shouldRun())->toBeTrue();
});

it('returns true for TestTransformer shouldRun', function () {
    $transformer = new TestTransformer;

    expect($transformer->shouldRun())->toBeTrue();
});

it('can override shouldRun to return false', function () {
    $transformer = new SkippableTransformer;

    expect($transformer->shouldRun())->toBeFalse();
});
