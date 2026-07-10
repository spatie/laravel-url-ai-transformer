---
title: Structured output
weight: 2
---

By default, a transformer stores the AI's free-form text response. When you need reliable, machine-readable data instead, a transformer can return structured output that is validated against a schema you define.

## Defining a schema

Implement Laravel AI's `HasStructuredOutput` contract and add a `schema()` method. The schema is built with the `JsonSchema` builder that is passed to the method.

```php
// app/Transformers/ProductTransformer.php
namespace App\Transformers;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class ProductTransformer extends Transformer implements HasStructuredOutput
{
    public function instructions(): Stringable|string
    {
        return 'Extract the product details from this webpage.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->required(),
            'price' => $schema->number()->required(),
            'in_stock' => $schema->boolean(),
        ];
    }
}
```

## How the result is stored

When a transformer defines a schema, the AI response is validated against it and stored as JSON on the transformation result. For the example above, `result` would contain something like:

```json
{"name":"Wireless keyboard","price":49.99,"in_stock":true}
```

You can retrieve and decode it like any other transformation result:

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

$result = TransformationResult::forUrl('https://example.com/product', 'product');

$product = json_decode($result, true);
```

## Testing structured transformers

Fake the transformer with an array to stand in for the structured response:

```php
use App\Transformers\ProductTransformer;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Illuminate\Support\Facades\Http;

it('extracts product details', function () {
    Http::fake([
        'https://example.com/product' => Http::response('<html>...</html>'),
    ]);

    ProductTransformer::fake([
        ['name' => 'Wireless keyboard', 'price' => 49.99, 'in_stock' => true],
    ]);

    Transform::urls('https://example.com/product')
        ->usingTransformers(new ProductTransformer);

    $this->artisan('transform-urls --now');

    $result = TransformationResult::forUrl('https://example.com/product', 'product');

    expect(json_decode($result, true))
        ->toMatchArray(['name' => 'Wireless keyboard']);
});
```
