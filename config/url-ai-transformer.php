<?php

use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction;
use Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

return [
    /*
     * The default AI provider and model that transformers use. The model may be
     * a plain string, or Model::Cheapest / Model::Smartest.
     *
     * Transformers can override these defaults. Learn how in the docs:
     * https://spatie.be/docs/laravel-url-ai-transformer/advanced-usage/customizing-ai-models
     */
    'ai' => [
        'provider' => Lab::OpenAI,
        'model' => 'gpt-4o-mini',
    ],

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
];
