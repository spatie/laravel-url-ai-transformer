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
        'provider' => Laravel\Ai\Enums\Lab::OpenAI,
        'model' => 'gpt-4o-mini',
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

This package uses the official [Laravel AI](https://github.com/laravel/ai) package under the hood to interact with various AI services. It provides a unified interface for working with different AI providers.

By default, the package is configured to use OpenAI. To get started, you'll need to add your OpenAI API key to your `.env` file:

```bash
OPENAI_API_KEY=your-api-key-here
```

### Using different AI providers

Laravel AI supports multiple providers including OpenAI, Anthropic Claude, Google Gemini, and more. Providers are represented by the `Laravel\Ai\Enums\Lab` enum. You can switch providers by updating the config file:

```php
'ai' => [
    'default' => [
        'provider' => Laravel\Ai\Enums\Lab::Anthropic,
        'model' => 'claude-haiku-4-5-20251001',
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

For more information about configuring providers, check out the [Laravel AI documentation](https://github.com/laravel/ai).

