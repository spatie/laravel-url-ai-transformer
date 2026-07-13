---
title: Getting started
weight: 1
---

Let's build a simple example that transforms blog posts into structured data. We'll use the `LdJsonTransformer` that comes with the package to extract structured information from web pages.

First, you should use the `Transform` class to register URLs to transform, and which transformer to use:

```php
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

// typically, in a service provider
Transform::urls(
    'https://spatie.be/blog',
    'https://spatie.be/open-source',
    'https://spatie.be/about-us'
)->usingTransformers(new LdJsonTransformer);
```

## Running transformations

Now, you can transform the URLs by running:

```bash
php artisan transform-urls
```

The command fetches each URL once and then dispatches a queued job for every transformer registered for that URL. Each queued job prepares the fetched content, sends it to the configured AI, and stores the response in the `transformation_results` table.

Start a queue worker to process those jobs:

```bash
php artisan queue:work
```

URL fetching happens synchronously while `transform-urls` is running. The AI transformations run on the queue, so retry, backoff, and queue middleware settings apply to the transformation itself.

During local development, or whenever you intentionally want to wait for all transformations, use `--now` to run every transformation job synchronously:

```bash
php artisan transform-urls --now
```

## What's in the database?

The `transformation_results` table stores all transformation data with the following fields:

- url: The URL that was transformed
- type: The transformer type (e.g., 'ldJson', 'summary')
- result: The AI-generated content stored as text
- successfully_completed_at: Timestamp when the transformation completed successfully
- latest_exception_seen_at: Timestamp of the most recent error (if any)
- latest_exception_message: The error message from the last failed attempt
- latest_exception_trace: Stack trace for debugging failed transformations
- created_at: When the record was first created
- updated_at: When the record was last modified

The `latest_exception` fields will be cleared when the transformation completes successfully.

## Retrieving transformation results

Here's how you can retrieve transformation results in your application.

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

// Get structured data for a specific URL
$ldJsonData = TransformationResult::forUrl('https://spatie.be/blog', 'ldJson');
```

The first parameter is the URL, the second parameter is the transformer type. By default, the transformer type is the camelCased class name of the transformer without the `Transformer` suffix. You can also pass the transformer class name instead of the type.

```php
$ldJsonData = TransformationResult::forUrl('https://spatie.be/blog', LdJsonTransformer::class);
```

If you need the underlying model instead of just the result, use `findForUrl()`:

```php
$transformationResult = TransformationResult::findForUrl('https://spatie.be/blog', 'ldJson');

$transformationResult->successfully_completed_at; // when the transformation last completed
```

## Scheduling transformations

To keep your transformations up to date, schedule the command to run periodically:

```php
// In routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('transform-urls')->dailyAt('02:00');
```
