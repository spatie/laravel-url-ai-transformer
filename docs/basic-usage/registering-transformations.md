---
title: Registering transformations
weight: 2
---

You can register URLs that should be transformed using the `Transform` class:

```php
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

Transform::urls('https://example.com/page')
    ->usingTransformers(new LdJsonTransformer);
```

You can register multiple URLs at once:

```php
Transform::urls(
    'https://example.com/page1',
    'https://example.com/page2',
    'https://example.com/page3'
)->usingTransformers(new LdJsonTransformer);
```

You can also give a closure. This logic will only be executed when the transformation is actually run:

```php
Transform::urls(
    fn() => Article::published()->pluck('url')->toArray()
)->usingTransformers(new LdJsonTransformer);
```

You can also register multiple transformers at once:

```php
Transform::urls('https://example.com/product')
    ->usingTransformers(
        new LdJsonTransformer,
        new ImageTransformer,
        new SummaryTransformer,
    );
```

## Where to register transformations

Typically, transformations are registered in a service provider:

```php
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
