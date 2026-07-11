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

This command will dispatch a queued job for each combination of URL and transformer. Inside that queued job:

- the content of the URL will be fetched.
- the content will be sent to the configured AI.
- the response from the AI will be stored in the `transformation_results` table.

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

