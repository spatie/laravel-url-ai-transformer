<?php

use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\ImageTransformer;
use \Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;
use \Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use \Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

it('will transform a webpage to ld', function() {
   Transform::urls('https://ohdear.app')->usingTransformers(new LdJsonTransformer());

   $this
       ->artisan(TransformUrlsCommand::class)
       ->assertSuccessful();

   $ld = TransformationResult::forUrl('https://spatie.be', 'ld');

   dd($ld);
});

it('can transform a webpage to an image', function() {
    Transform::urls('https://spatie.be/blog/how-to-make-your-ai-agent-program-with-grace-and-style')->usingTransformers(new ImageTransformer());

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

});
