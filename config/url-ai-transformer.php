<?php

use Prism\Prism\Enums\Provider;
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
     * By default, the transformers that ship with this package leverage the wonderful
     * Prism package to interact with various AI services.
     *
     * https://prismphp.com
     *
     * You can customize the default settings here.
     */
    'ai' => [
        'default' => [
            'provider' => Provider::OpenAI,
            'model' => 'gpt-4o-mini',
        ],

        'image' => [
            'provider' => Provider::OpenAI,
            'model' => 'dall-e-3',
        ],
    ],
];
