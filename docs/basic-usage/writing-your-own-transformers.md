---
title: Writing your own transformers
weight: 3
---

The real power of this package comes from writing your own transformers. Let's explore how to create custom transformers that fit your specific needs.

## Creating a basic transformer

All transformers extend the `Transformer` base class and implement two required methods `transform` and `getPrompt`.

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

        // you should set the result property on the transformation result model to store the result
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


