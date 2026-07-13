---
title: Customizing AI models
weight: 2
---

By default, transformers use the provider and cheapest model configured in `config/url-ai-transformer.php`. You can override either one for a specific transformer, tune other AI options, or skip AI entirely.

This package uses the official [Laravel AI](https://github.com/laravel/ai) package under the hood for all AI interactions. Because every transformer is a Laravel AI agent, you can tune options like temperature and max tokens by adding attributes to the transformer class.

```php
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[MaxTokens(1000)]
#[Temperature(0.1)]
class PreciseTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Extract the key facts as accurately as possible.';
    }
}

#[MaxTokens(2000)]
#[Temperature(0.8)]
class CreativeTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Rewrite this webpage as an engaging summary.';
    }
}
```

## Choosing a provider and model per transformer

By default, transformers use the `provider` and `model` from `config/url-ai-transformer.php`. A transformer can override these values with Laravel AI attributes or methods.

Use `#[UseCheapestModel]` or `#[UseSmartestModel]` to let the provider pick the right model without naming it. The configured provider is respected, only the model changes.

```php
use Laravel\Ai\Attributes\UseSmartestModel;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[UseSmartestModel]
class ThoroughTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Produce a detailed, high quality summary.';
    }
}
```

Use `#[Model]` to pin a specific model, or `#[Provider]` to use a different provider entirely (in which case its default model is used unless you also add `#[Model]`).

```php
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
class ClaudeTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage.';
    }
}
```

Methods are useful when the provider or model needs to be selected dynamically. Laravel AI checks these methods before its attributes:

```php
use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

class ClaudeTransformer extends Transformer
{
    public function provider(): Lab
    {
        return Lab::Anthropic;
    }

    public function model(): string
    {
        return 'claude-haiku-4-5-20251001';
    }

    public function instructions(): Stringable|string
    {
        return 'Summarize this webpage.';
    }
}
```

If you define `provider()` without `model()`, Laravel AI uses that provider's default model.

For detailed information about the available providers, models, and options, check out the [Laravel AI documentation](https://github.com/laravel/ai).

## A cheapest or smartest default model

The `model` in your config may be a plain string, or one of the `Model` enum cases. `Model::Cheapest` and `Model::Smartest` let the configured provider pick the model for you, so you don't have to track model names.

```php
// config/url-ai-transformer.php
use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Enums\Model;

'ai' => [
    'provider' => Lab::OpenAI,
    'model' => Model::Cheapest,
],
```

Individual transformers can still override this with the attributes above.

## Transformers without AI

Not all transformers need to use AI. You can create transformers that process content using traditional methods:

```php
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class WordCountTransformer extends Transformer
{
    public function transform(): void
    {
        $wordCount = str_word_count(strip_tags($this->urlContent));

        $this->transformationResult->result = (string) $wordCount;
    }
}
```
