<?php

use Spatie\LaravelUrlAiTransformer\Support\Transform;
use \Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;
use \Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use \Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

it('will transform a webpage to ld', function() {
   Transform::urls('https://spatie.be')->usingTransformers(new LdJsonTransformer());

   $this
       ->artisan(TransformUrlsCommand::class)
       ->assertSuccessful();

   $ld = TransformationResult::forUrl('https://spatie.be')->result('ld');

   dd($ld);
});
