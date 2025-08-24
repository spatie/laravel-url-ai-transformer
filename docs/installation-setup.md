---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-url-ai-transformer
```

## Publishing the config file

Optionally, you can publish the config file with this command:

```bash
php artisan vendor:publish --tag="url-ai-transformer-config"
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

## Configuring AI providers

This package uses [Prism](https://prismphp.com) under the hood to interact with various AI services. Prism is a powerful, framework-agnostic PHP library that provides a unified interface for working with different AI providers.

By default, the package is configured to use OpenAI's GPT-4 models. To get started, you'll need to add your OpenAI API key to your `.env` file:

```bash
OPENAI_API_KEY=your-api-key-here
```

### Using different AI providers

Prism supports multiple AI providers including OpenAI, Anthropic Claude, Google Gemini, and more. You can easily switch providers by updating the config file:

```php
'ai' => [
    'default' => [
        'provider' => Prism\Prism\Enums\Provider::Anthropic,
        'model' => 'claude-3-5-sonnet-20241022',
    ],
],
```

Don't forget to add the corresponding API key to your `.env` file:

```bash
# For Anthropic Claude
ANTHROPIC_API_KEY=your-api-key-here

# For Google Gemini
GEMINI_API_KEY=your-api-key-here
```

For more information about configuring Prism and the available providers, check out the [Prism documentation](https://prismphp.com/docs/providers).

