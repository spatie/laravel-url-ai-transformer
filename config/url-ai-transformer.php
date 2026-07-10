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
     * The default AI provider and model that transformers use. Under the hood,
     * this package leverages the official Laravel AI package to interact with
     * various AI services.
     *
     * https://github.com/laravel/ai
     *
     * Individual transformers may override these defaults using Laravel AI's
     * attributes, like #[Model], #[Provider], #[UseCheapestModel] and
     * #[UseSmartestModel].
     */
    'ai' => [
        'provider' => Lab::OpenAI,
        'model' => 'gpt-4o-mini',
    ],
];
