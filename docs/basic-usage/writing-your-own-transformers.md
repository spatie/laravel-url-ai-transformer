---
title: Writing your own transformers
weight: 3
---

The real power of this package comes from writing your own transformers. Let's explore how to create custom transformers that fit your specific needs.

## Creating a basic transformer

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

The base `Transformer` runs the AI call for you and stores the response on the transformation result. If you want to tweak the content that gets sent to the AI, override the `content()` method:

```php
class SummaryTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage content in 3 concise bullet points.';
    }

    public function content(): string
    {
        return str(strip_tags($this->urlContent))->limit(6000);
    }
}
```

You can now use your transformer:

```php
Transform::urls('https://example.com/article')
    ->usingTransformers(new SummaryTransformer);
```

When a transformer runs, it has access to three properties:

- `$this->url` - The URL being transformed
- `$this->urlContent` - The fetched content from the URL
- `$this->transformationResult` - The database model where you store results


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

````


