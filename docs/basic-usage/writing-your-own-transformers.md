---
title: Writing your own transformers
weight: 3
---

The real power of this package comes from writing your own transformers. Let's explore how to create custom transformers
that fit your specific needs.

## Creating a basic transformer

All transformers extend the `Transformer` base class and implement two required methods:

```php
// app/Transformers/SummaryTransformer.php
namespace App\Transformers;

use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Prism\Prism\Prism;
use Spatie\LaravelUrlAiTransformer\Support\Config;

class SummaryTransformer extends Transformer
{
    public function transform(): void
    {
        $response = Prism::text()
            ->using(Config::aiProvider(), Config::aiModel())
            ->withPrompt($this->getPrompt())
            ->asText();

        $this->transformationResult->result = $response->text;
    }

    public function getPrompt(): string
    {
        return "Summarize this webpage content in 3 concise bullet points: \n\n" 
            . $this->urlContent;
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

## Conditional transformations

Sometimes you only want to run a transformer under certain conditions.

You can add an option method `shouldRun` to your transformer. If `shouldRun` returns `false`, the transformer is skipped. This
example only runs the transformation if it hasn't been successfully completed in the last 30 days.

```php
class MonthlyReportTransformer extends Transformer
{
    public function shouldRun(): bool
    {
        // Run if we've never transformed or if it's been more than 30 days
        return $this
            ->transformationResult
            ->successfully_completed_at?
            ->diffInDays() > 30 ?? true;
    }

    public function transform(): void
    {
        // Your transformation logic
    }

    public function getPrompt(): string
    {
        return "Generate a monthly report summary...";
    }
}
```

## Chaining multiple transformers

You can apply multiple transformers to the same URL:

```php
Transform::urls('https://example.com/product')
    ->usingTransformers(
        new ProductDataTransformer,
        new SeoMetaTransformer,
        new SocialMediaTransformer
    );
```

Each transformer runs independently and stores its own result.

## Custom transformer types

By default, the transformer type is derived from the class name. You can override this:

```php
class MyCustomTransformer extends Transformer
{
    public function type(): string
    {
        return 'custom_analysis';
    }

    // Other methods...
}
```


