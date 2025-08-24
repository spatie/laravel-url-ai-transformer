---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-url-ai-transformer
```

## Publishing the config file

Optionally, you can publish the `health` config file with this command.

```bash
php artisan vendor:publish --tag="health-config"
```

This is the content of the published config file:

```php
return [
    /*
     * The model that will be used to store the transformation results.
     *
     * You can use your own model by extending the default model.
     */
    'model' => Spatie\LaravelUrlAiTransformer\Models\TransformationResult::class,

    /*
     * The actions that will perform low-level operations of the package.
     *
     * You can extend the default actions and specify your own actions here
     * to customize the package's behavior.
     */
    'actions' => [
        'fetch_url_content' => Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction::class,
        'process_registration' => Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction::class,
    ],

    /*
     * The jobs that will handle background processing.
     *
     * You can extend the default jobs and specify your own jobs here
     * to customize the package's behavior.
     */

    'process_transformer_job' => Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob::class,

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
            'provider' => Prism\Prism\Enums\Provider::OpenAI,
            'model' => 'gpt-4o-mini',
        ],

        'image' => [
            'provider' => Prism\Prism\Enums\Provider::OpenAI,
            'model' => 'dall-e-3',
        ],
    ],
];
```

## Migrating the database

This package stored transformations results in the database. To create the `transformation_results` table, you must create and run the migration.

```bash
php artisan vendor:publish --tag="url-ai-transformer-migrations"
php artisan migrate
```

## Configuring prism

TODO: explain that prism is used under the hood for the ai tasks. Add link to prism docs. Give simple example of how to set up environment keys for the prism package (take a look at the current config to know which provider is used).

