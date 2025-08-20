<?php

use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\TestTransformer;

it('can register a single url', function () {
    Transform::urls('https://example.com/')
        ->usingTransformers(new TestTransformer());

    $registrations = app(RegisteredTransformations::class)->all();
    
    expect($registrations)->toHaveCount(1);
    
    $registration = $registrations[0];
    $urls = iterator_to_array($registration->getUrls());
    
    expect($urls)->toBe(['https://example.com/']);
    expect($registration->getTransformers())->toHaveCount(1);
    expect($registration->getTransformers()[0])->toBeInstanceOf(TestTransformer::class);
});

it('can register multiple urls at once', function () {
    Transform::urls('https://example.com/', 'https://another.com/')
        ->usingTransformers(new TestTransformer());

    $registrations = app(RegisteredTransformations::class)->all();
    
    expect($registrations)->toHaveCount(1);
    
    $registration = $registrations[0];
    $urls = iterator_to_array($registration->getUrls());
    
    expect($urls)->toBe(['https://example.com/', 'https://another.com/']);
});

it('can register urls as an array', function () {
    Transform::urls(['https://example.com/', 'https://another.com/'])
        ->usingTransformers(new TestTransformer());

    $registrations = app(RegisteredTransformations::class)->all();
    
    expect($registrations)->toHaveCount(1);
    
    $registration = $registrations[0];
    $urls = iterator_to_array($registration->getUrls());
    
    expect($urls)->toBe(['https://example.com/', 'https://another.com/']);
});

it('can register urls using closures', function () {
    Transform::urls(
        fn() => 'https://example.com/',
        'https://another.com/',
        fn() => 'https://third.com/'
    )->usingTransformers(new TestTransformer());

    $registrations = app(RegisteredTransformations::class)->all();
    
    expect($registrations)->toHaveCount(1);
    
    $registration = $registrations[0];
    $urls = iterator_to_array($registration->getUrls());
    
    expect($urls)->toBe(['https://example.com/', 'https://another.com/', 'https://third.com/']);
});

it('can register multiple transformations separately', function () {
    Transform::urls('https://example.com/')
        ->usingTransformers(new TestTransformer());
    
    Transform::urls('https://another.com/')
        ->usingTransformers(new TestTransformer());

    $registrations = app(RegisteredTransformations::class)->all();
    
    expect($registrations)->toHaveCount(2);
    
    $urls1 = iterator_to_array($registrations[0]->getUrls());
    $urls2 = iterator_to_array($registrations[1]->getUrls());
    
    expect($urls1)->toBe(['https://example.com/']);
    expect($urls2)->toBe(['https://another.com/']);
});
