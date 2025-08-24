---
title: Registering transformations
weight: 2
---

Before you can transform URLs, you need to register them with the package. This page covers all the ways to register URLs and transformers.

## Basic registration

The simplest way to register URLs is using the `Transform` facade:

```php
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

Transform::urls('https://example.com/page')
    ->usingTransformers(new LdJsonTransformer);
```

## Where to register transformations

The best place to register transformations is in a service provider:

```php
// app/Providers/UrlTransformationServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

class UrlTransformationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Transform::urls(
            'https://example.com/about',
            'https://example.com/products',
            'https://example.com/contact'
        )->usingTransformers(new LdJsonTransformer);
    }
}
```

Don't forget to register the service provider in `config/app.php`:

```php
'providers' => [
    // Other providers...
    App\Providers\UrlTransformationServiceProvider::class,
],
```

## Multiple URLs at once

Register multiple URLs in a single call:

```php
// As separate arguments
Transform::urls(
    'https://example.com/page1',
    'https://example.com/page2',
    'https://example.com/page3'
)->usingTransformers(new LdJsonTransformer);

// As an array
Transform::urls([
    'https://example.com/page1',
    'https://example.com/page2',
    'https://example.com/page3'
])->usingTransformers(new LdJsonTransformer);
```

## Dynamic URLs with closures

Sometimes you need to transform URLs that change over time. Use closures to dynamically generate URLs:

```php
use App\Models\BlogPost;

Transform::urls(
    fn() => BlogPost::published()->pluck('url')->toArray()
)->usingTransformers(new LdJsonTransformer);
```

This will transform all published blog post URLs whenever the command runs.

### More closure examples

```php
// Transform recent articles
Transform::urls(
    fn() => Article::where('created_at', '>', now()->subDays(7))
        ->pluck('url')
        ->toArray()
)->usingTransformers(new SummaryTransformer);

// Transform products in specific categories
Transform::urls(
    fn() => Product::whereIn('category', ['electronics', 'books'])
        ->pluck('product_url')
        ->toArray()
)->usingTransformers(new ProductDataTransformer);

// Combine static and dynamic URLs
Transform::urls(
    'https://example.com/home',
    fn() => Page::featured()->pluck('url')->toArray(),
    'https://example.com/contact'
)->usingTransformers(new LdJsonTransformer);
```

## Multiple transformers

Apply multiple transformers to the same URLs:

```php
Transform::urls('https://example.com/product')
    ->usingTransformers(
        new LdJsonTransformer,
        new ImageTransformer,
        new SummaryTransformer
    );
```

Each transformer runs independently and stores its own result in the database.

## Separate registrations

You can register different transformers for different sets of URLs:

```php
// Blog posts get summaries and structured data
Transform::urls(
    fn() => BlogPost::pluck('url')->toArray()
)->usingTransformers(
    new LdJsonTransformer,
    new SummaryTransformer
);

// Product pages get structured data and images
Transform::urls(
    fn() => Product::pluck('url')->toArray()
)->usingTransformers(
    new LdJsonTransformer,
    new ImageTransformer,
    new ProductDataTransformer
);

// Static pages only get structured data
Transform::urls(
    'https://example.com/about',
    'https://example.com/contact'
)->usingTransformers(new LdJsonTransformer);
```

## Conditional registration

Register transformations based on environment or configuration:

```php
public function boot(): void
{
    // Only transform in production
    if (app()->environment('production')) {
        Transform::urls('https://example.com/api-docs')
            ->usingTransformers(new DocumentationTransformer);
    }
    
    // Register based on feature flag
    if (config('features.ai_summaries')) {
        Transform::urls(
            fn() => Article::pluck('url')->toArray()
        )->usingTransformers(new SummaryTransformer);
    }
    
    // Different transformers per environment
    $transformer = app()->environment('production')
        ? new LdJsonTransformer
        : new DebugTransformer;
        
    Transform::urls('https://example.com')
        ->usingTransformers($transformer);
}
```

## URL formats

The package accepts any URL format:

```php
Transform::urls(
    'https://example.com/full-url',        // Full URLs
    '/relative-path',                      // Relative paths
    'another/path',                         // Paths without leading slash
    '/',                                    // Root path
    'https://api.example.com/v1/users'     // API endpoints
)->usingTransformers(new LdJsonTransformer);
```

URLs are stored exactly as provided, so be consistent with your format.

## Best practices

1. **Use service providers**: Keep all registrations in one place
2. **Group related URLs**: Register URLs with similar transformers together
3. **Use closures for dynamic content**: Let the database be your source of truth
4. **Be consistent with URL formats**: Use either full URLs or relative paths consistently
5. **Document your registrations**: Add comments explaining what each registration does

## What's next?

- Learn how to [run transformations](../advanced-usage/using-the-command.md)
- Start [writing your own transformers](./writing-your-own-transformers)
- Explore [getting started examples](./getting-started)
