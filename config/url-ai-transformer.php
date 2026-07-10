<?php

use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction;
use Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

return [
    /*
     * The model that will be used to store the transformation results.
     *
     * You can use your own model by extending the default model.
     */
    'model' => TransformationResult::class,

    /*
     * The actions that will perform low-level operations of the package.
     *
     * You can extend the default actions and specify your own actions here
     * to customize the package's behavior.
     */
    'actions' => [
        'fetch_url_content' => FetchUrlContentAction::class,
        'process_registration' => ProcessRegistrationAction::class,
    ],

    /*
     * The jobs that will handle background processing.
     *
     * You can extend the default jobs and specify your own jobs here
     * to customize the package's behavior.
     */

    'process_transformer_job' => ProcessTransformerJob::class,

    /*
     * By default, the transformers that ship with this package leverage the official
     * Laravel AI package to interact with various AI services.
     *
     * https://github.com/laravel/ai
     *
     * You can customize the default settings here.
     */
    'ai' => [
        'default' => [
            'provider' => Lab::OpenAI,
            'model' => 'gpt-4o-mini',
        ],

        'image' => [
            'provider' => Lab::OpenAI,
            'model' => 'dall-e-3',
        ],
    ],
];
