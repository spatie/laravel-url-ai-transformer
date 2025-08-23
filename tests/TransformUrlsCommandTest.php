<?php

use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\ImageTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

it('can transform an URL', function() {
   Transform::urls('https://spatie.be')->usingTransformers(new DummyLdTransformer());

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

   expect(TransformationResult::forUrl('https://spatie.be', 'ld'))->toBe('dummy result');
});

it('can transform a webpage to an image', function() {
    Transform::urls('https://spatie.be/blog/how-to-make-your-ai-agent-program-with-grace-and-style')->usingTransformers(new ImageTransformer());

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

})->skip();
