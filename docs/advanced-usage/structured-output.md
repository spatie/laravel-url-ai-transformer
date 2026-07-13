---
title: Structured output
weight: 1
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

## Forcing valid JSON while keeping the structure flexible

A structured schema normally describes each field in advance. When you want guaranteed valid JSON but the shape of the data should stay open, wrap the free-form part in a single string property:

```php
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class LdJsonTransformer extends Transformer implements HasStructuredOutput
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage to ld+json. Put the ld+json in the `json` key.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'json' => $schema->string()->required(),
        ];
    }

    protected function resultFrom(AgentResponse $response): string
    {
        $json = $response instanceof StructuredAgentResponse
            ? $response['json']
            : $response->text;

        json_decode($json, flags: JSON_THROW_ON_ERROR);

        return $json;
    }
}
```

The schema makes the provider return an outer object with a `json` string. `json_decode()` then validates that string before it is stored. Invalid JSON throws, which marks the transformation as failed and allows the queued job to retry. The built-in `LdJsonTransformer` uses this technique.

## Testing structured transformers

Fake the transformer with an array to stand in for the structured response:

```php
use App\Transformers\ProductTransformer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;

it('extracts product details', function () {
    Http::fake([
        'https://example.com/product' => Http::response('<html>...</html>'),
    ]);

    ProductTransformer::fake([
        ['name' => 'Wireless keyboard', 'price' => 49.99, 'in_stock' => true],
    ]);

    Transform::urls('https://example.com/product')
        ->usingTransformers(new ProductTransformer);

    Artisan::call('transform-urls', ['--now' => true]);

    $result = TransformationResult::forUrl('https://example.com/product', 'product');

    expect($result)->not->toBeNull();

    $product = json_decode($result, associative: true, flags: JSON_THROW_ON_ERROR);

    expect($product['name'])->toBe('Wireless keyboard');
});
```
