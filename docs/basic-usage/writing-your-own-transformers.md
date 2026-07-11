---
title: Writing your own transformers
weight: 3
---

The real power of this package comes from writing your own transformers. Let's explore how to create custom transformers that fit your specific needs.

## Creating a basic transformer

You can generate a transformer with the `make:transformer` command:

```bash
php artisan make:transformer SummaryTransformer
```

This creates a transformer class in `app/Transformers`.

Every transformer is a [Laravel AI](https://github.com/laravel/ai) agent. Extend the `Transformer` base class and implement `instructions()`, which returns the AI instructions to follow. The fetched URL content is sent along automatically as the prompt.

```php
// app/Transformers/SummaryTransformer.php
namespace App\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class SummaryTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage content in 3 concise bullet points.';
    }
}
```

The base `Transformer` runs the AI call for you and stores the response on the transformation result.

## Customizing the content that is sent to the AI

By default, the fetched URL content is cleaned up before it is sent to the AI: scripts and styles are removed, HTML tags are stripped, whitespace is collapsed, and the result is limited to 6000 characters. This work is done by the `PrepareUrlContentAction`, which you can [replace with your own action](../advanced-usage/overriding-actions) to change the behavior for all transformers.

To tweak the content for a single transformer, override the `content()` method:

```php
class SummaryTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage content in 3 concise bullet points.';
    }

    public function content(): string
    {
        return (string) str(strip_tags($this->urlContent))->limit(1000);
    }
}
```

You can now use your transformer:

```php
Transform::urls('https://example.com/article')
    ->usingTransformers(new SummaryTransformer);
```

When a transformer runs, it has access to three properties:

- `$this->url`: the URL being transformed
- `$this->urlContent`: the fetched content from the URL
- `$this->transformationResult`: the database model where you store results

## Returning structured output

Instead of free-form text, a transformer can return validated, machine-readable data by defining a schema. See [Structured output](../advanced-usage/structured-output) for the details.

## Customizing the stored result

By default the transformer stores the AI's text response. You can post-process it, or save extra data on the model, by overriding `resultFrom()`. See [Customizing the stored result](../advanced-usage/customizing-the-result) for the details.

## Custom transformer types

By default, the transformer type is derived from the class name. You can override this:

```php
class MyCustomTransformer extends Transformer
{
    public function type(): string
    {
        return 'customType';
    }

    // Other methods...
}
```

You can use your custom type when retrieving a transformation result:

```php
$ldJsonData = TransformationResult::forUrl('https://spatie.be/blog', 'customType');
```


