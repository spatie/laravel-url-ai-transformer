---
title: Basic usage
weight: 1
---

This section covers the fundamental features of the package.

1. [Get started with a basic example](./getting-started)
2. [Register your transformations](./registering-transformations)
3. [Write your own transformers](./writing-your-own-transformers)

## The built-in LdJsonTransformer

The package ships with one transformer out of the box: `LdJsonTransformer`. It extracts structured data from web pages in [ld+json](https://json-ld.org) format, following Schema.org standards.

```php
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

Transform::urls('https://example.com/article')
    ->usingTransformers(new LdJsonTransformer);
```

For anything else, [writing your own transformer](./writing-your-own-transformers) only takes a few lines of code.
