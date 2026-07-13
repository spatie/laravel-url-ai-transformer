# Upgrading from v1 to v2

Version 2 replaces the direct Prism integration with Laravel AI, makes the default transformers Laravel AI agents, and requires Laravel 12 or 13.

## Requirements

- PHP 8.4 or higher
- Laravel 12 or 13

Update the package and its dependencies:

```bash
composer require spatie/laravel-url-ai-transformer:^2.0 --with-all-dependencies
```

The package no longer depends on `prism-php/prism`. If your application does not use Prism directly, Composer can remove it during the update.

## Update the config file

Back up any customizations and republish the config file:

```bash
php artisan vendor:publish --tag="url-ai-transformer-config" --force
```

The AI config is now flat. Providers use Laravel AI's `Lab` enum, and the default model is the cheapest model offered by the configured provider.

```php
use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Enums\Model;

'ai' => [
    'provider' => Lab::OpenAI,
    'model' => Model::Cheapest,
],
```

Replace these v1 config values:

```php
use Prism\Prism\Enums\Provider;

'ai' => [
    'default' => [
        'provider' => Provider::OpenAI,
        'model' => 'gpt-4o-mini',
    ],
],
```

with the new `ai.provider` and `ai.model` values. The model may be a model name, `Model::Cheapest`, or `Model::Smartest`.

The unused `ai.image` config has been removed. A new `actions.prepare_url_content` key is required; republishing the config adds the default `PrepareUrlContentAction`.

## Update custom transformers

The base `Transformer` is now a Laravel AI agent. For the common case, replace `getPrompt()` and the Prism request in `transform()` with an `instructions()` method:

```php
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class SummaryTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage in three concise bullet points.';
    }
}
```

The base class now:

- obtains the user prompt from `content()`;
- sends that text to Laravel AI as the user prompt;
- uses `instructions()` as the agent's system instructions;
- stores the response on the transformation result.

By default, `content()` removes scripts, styles, and HTML, decodes HTML entities, collapses whitespace, and limits the result to 6,000 characters. Override `content()` when a transformer needs different input.

You can still override `transform()` for transformations that do not use AI. Override `resultFrom()` when you only need to post-process Laravel AI's response or populate extra columns on the transformation result.

Laravel AI attributes such as `#[Provider]`, `#[Model]`, `#[UseCheapestModel]`, `#[UseSmartestModel]`, `#[Temperature]`, and `#[MaxTokens]` can configure an individual transformer. You may also define `provider()` and `model()` methods on the transformer.

## Update transformer tests

Replace Prism fakes with a fake on the transformer itself:

```php
SummaryTransformer::fake([
    "First point\nSecond point\nThird point",
]);
```

Structured transformers should fake an array matching their schema:

```php
ProductTransformer::fake([
    ['name' => 'Wireless keyboard', 'price' => 49.99],
]);
```

The built-in `LdJsonTransformer` expects a `json` string containing valid JSON:

```php
LdJsonTransformer::fake([
    ['json' => '{"@context":"https://schema.org","@type":"WebPage"}'],
]);
```

Invalid JSON now fails the transformation instead of being stored.

## Add the composite unique index

New installations enforce one transformation result per URL and transformer type with a unique index on `url` and `type`. Published migrations are not updated automatically, so existing applications should add an application migration.

First resolve any duplicate `url` and `type` pairs. Then use a migration like this:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transformation_results', function (Blueprint $table): void {
            $table->dropIndex(['url']);
            $table->unique(['url', 'type']);
        });
    }
};
```

If you renamed or removed the original URL index, adjust the `dropIndex()` call accordingly.

Run the migration with `php artisan migrate`.

## Update regeneration calls

`regenerate()` always dispatches a queued job. Replace synchronous v1 calls:

```php
$transformationResult->regenerate(true);
```

with:

```php
$transformationResult->regenerateNow();
```

Calls to `$transformationResult->regenerate()` remain queued.

## Update custom package classes

If your application extends package internals, make these signature and inheritance changes:

- `ProcessRegistrationAction::execute()` now returns the number of jobs successfully dispatched or run as an `int`.
- A custom transformation result model must extend `Spatie\LaravelUrlAiTransformer\Models\TransformationResult`.
- A custom process job must extend `Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob`.

Failed transformation jobs now rethrow after recording the exception and firing `TransformerFailed`. Laravel queue retry, backoff, and middleware settings therefore take effect. The failure event can be fired more than once when a job is retried.

## Run the queue worker

`transform-urls` fetches each URL synchronously and dispatches its transformation jobs to the queue. Make sure a queue worker is running:

```bash
php artisan queue:work
```

Use `php artisan transform-urls --now` when you intentionally want to run all transformations synchronously.
