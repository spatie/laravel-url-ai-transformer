<?php

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

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
