---
title: Customizing the stored result
weight: 3
---

By default a transformer stores the AI's text response, or, for a [structured output](./structured-output) transformer, its structured JSON. You can change what gets stored by overriding the `resultFrom()` method on your transformer.

`resultFrom()` receives the Laravel AI response, and whatever you return is stored in the `result` column.

## Manipulating the response

Return a modified string to post-process the response before it is stored.

```php
use Laravel\Ai\Responses\AgentResponse;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class SummaryTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage.';
    }

    protected function resultFrom(AgentResponse $response): string
    {
        return trim(strip_tags($response->text));
    }
}
```

## Saving extra data on the model

The record that gets saved is available on the transformer as `$this->transformationResult`, so you can set additional attributes on it from within `resultFrom()`.

```php
protected function resultFrom(AgentResponse $response): string
{
    $summary = trim($response->text);

    $this->transformationResult->word_count = str_word_count($summary);

    return $summary;
}
```

To persist extra columns like `word_count`, add them to a migration and point the package at your own model via the `model` config key.

## Controlling persistence

`resultFrom()` runs while the transformer is producing the result. If you need to change how results are persisted more broadly (for example, saving to another table, or running side effects), extend the process transformer job and register it with the `process_transformer_job` config key, or listen for the `TransformerEnded` event.
