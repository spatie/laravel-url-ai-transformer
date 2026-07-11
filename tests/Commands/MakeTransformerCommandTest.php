<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(app_path('Transformers'));
});

afterEach(function () {
    File::deleteDirectory(app_path('Transformers'));
});

it('can generate a transformer class', function () {
    $this
        ->artisan('make:transformer', ['name' => 'SummaryTransformer'])
        ->assertSuccessful();

    $generatedPath = app_path('Transformers/SummaryTransformer.php');

    expect(File::exists($generatedPath))->toBeTrue();

    $contents = File::get($generatedPath);

    expect($contents)
        ->toContain('namespace App\Transformers;')
        ->toContain('class SummaryTransformer extends Transformer')
        ->toContain('public function instructions(): Stringable|string');
});
