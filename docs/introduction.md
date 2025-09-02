---
title: Introduction
weight: 1
---

Using this package, you can transform URLs and their content using AI. Whether you want to extract structured data, generate summaries, create image, or apply custom AI transformations to web content - this package can do it.

The result of the transformation is stored in a database. You can retrieve the transformed content at any time.

Here's how you can transform a blog post into structured [ld+json data](https://json-ld.org) using AI:

```php
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

Transform::urls('https://example.com/blog/my-post')
    ->usingTransformers(new LdJsonTransformer);
```

A transformer is a class where you can configure the AI transformation, and specify the prompt to use.

The configured transformation can be run using the `transform-urls` command.

```bash
php artisan transform-urls
```

After the transformation is complete, you can retrieve the transformed content using the `TransformationResult` model.

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

$structuredData = TransformationResult::forUrl('https://example.com/blog/my-post','ldJson');
```
