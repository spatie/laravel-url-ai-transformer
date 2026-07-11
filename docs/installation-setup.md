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
     * The default AI provider and model that transformers use. The model may be
     * a plain string, or Model::Cheapest / Model::Smartest.
     *
     * Transformers can override these defaults. Learn how in the docs:
     * https://spatie.be/docs/laravel-url-ai-transformer/advanced-usage/customizing-ai-models
     */
    'ai' => [
        'provider' => Laravel\Ai\Enums\Lab::OpenAI,
        'model' => Spatie\LaravelUrlAiTransformer\Enums\Model::Smartest,
    ],

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
        'prepare_url_content' => Spatie\LaravelUrlAiTransformer\Actions\PrepareUrlContentAction::class,
        'process_registration' => Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction::class,
    ],

    /*
     * The job that will process transformations in the background.
     *
     * You can extend the default job and specify your own job here
     * to customize the package's behavior.
     */
    'process_transformer_job' => Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob::class,
];
```

## Migrating the database

This package stores transformation results in the database. To create the `transformation_results` table, you must publish and run the migration.

```bash
php artisan vendor:publish --tag="url-ai-transformer-migrations"
php artisan migrate
```

## Configuring AI providers

This package uses the official [Laravel AI](https://github.com/laravel/ai) package under the hood to interact with various AI services. It provides a unified interface for working with different AI providers.

By default, the package is configured to use OpenAI. To get started, you'll need to add your OpenAI API key to your `.env` file:

```bash
OPENAI_API_KEY=your-api-key-here
```

### Using different AI providers

Laravel AI supports multiple providers including OpenAI, Anthropic Claude, Google Gemini, and more. Providers are represented by the `Laravel\Ai\Enums\Lab` enum. You can switch providers by updating the config file:

```php
'ai' => [
    'provider' => Laravel\Ai\Enums\Lab::Anthropic,
    'model' => 'claude-haiku-4-5-20251001',
],
```

Don't forget to add the corresponding API key to your `.env` file:

```bash
# For Anthropic Claude
ANTHROPIC_API_KEY=your-api-key-here

# For Google Gemini
GEMINI_API_KEY=your-api-key-here
```

For more information about configuring providers, check out the [Laravel AI documentation](https://github.com/laravel/ai).

