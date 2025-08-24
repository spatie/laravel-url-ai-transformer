---
title: Basic usage
weight: 1
---

This section covers the fundamental features of the Laravel URL AI Transformer package.

## Available transformers

The package ships with two powerful transformers out of the box:

### LdJsonTransformer

Extracts structured data from web pages in LD+JSON format. This transformer analyzes the content and generates comprehensive structured data following Schema.org standards.

```php
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

Transform::urls('https://example.com/article')
    ->usingTransformers(new LdJsonTransformer);
```

Perfect for:
- SEO optimization
- Content aggregation
- Building knowledge graphs
- Creating rich snippets

### ImageTransformer

Generates images based on web content using AI image generation models. This transformer reads the content and creates visual representations.

```php
use Spatie\LaravelUrlAiTransformer\Transformers\ImageTransformer;

Transform::urls('https://example.com/blog-post')
    ->usingTransformers(new ImageTransformer);
```

Perfect for:
- Creating social media images
- Generating visual summaries
- Building image galleries from text content
- Creating infographics

## Quick start

1. [Register your transformations](./registering-transformations)
2. [Get started with basic examples](./getting-started)
3. [Use the command](../advanced-usage/using-the-command.md)
4. [Write your own transformers](./writing-your-own-transformers)

## Common patterns

### Transform multiple URLs at once

```php
Transform::urls(
    'https://example.com/page1',
    'https://example.com/page2',
    'https://example.com/page3'
)->usingTransformers(new LdJsonTransformer);
```

### Apply multiple transformers

```php
Transform::urls('https://example.com/article')
    ->usingTransformers(
        new LdJsonTransformer,
        new ImageTransformer,
        new CustomSummaryTransformer
    );
```

### Use closures for dynamic URLs

```php
Transform::urls(
    fn() => Article::published()->pluck('url')->toArray()
)->usingTransformers(new LdJsonTransformer);
```
