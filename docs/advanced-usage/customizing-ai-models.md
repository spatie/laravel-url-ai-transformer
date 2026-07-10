---
title: Customizing AI models
weight: 3
---

By default, transformers use the AI provider and model configured in your `config/url-ai-transformer.php` file. However, you can customize AI options on a per-transformer basis, or even skip AI entirely.

This package uses the official [Laravel AI](https://github.com/laravel/ai) package under the hood for all AI interactions. Because every transformer is a Laravel AI agent, you can tune options like temperature and max tokens by adding attributes to the transformer class.

```php
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Stringable;

#[MaxTokens(1000)]
#[Temperature(0.1)] // Low temperature for precise, consistent output
class PreciseTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Extract the key facts as accurately as possible.';
    }
}

#[MaxTokens(2000)]
#[Temperature(0.8)] // Higher temperature for creative output
class CreativeTransformer extends Transformer
{
    public function instructions(): Stringable|string
    {
        return 'Rewrite this webpage as an engaging summary.';
    }
}
```

## Choosing a provider and model per transformer

By default, transformers use the `provider` and `model` from `config/url-ai-transformer.php`. A transformer can override this with Laravel AI's attributes.

Use `#[UseCheapestModel]` or `#[UseSmartestModel]` to let the provider pick the right model without naming it. The configured provider is respected, only the model changes.

```php
use Laravel\Ai\Attributes\UseSmartestModel;

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

For detailed information about the available providers, models, and options, check out the [Laravel AI documentation](https://github.com/laravel/ai).

## Transformers without AI

Not all transformers need to use AI. You can create transformers that process content using traditional methods:

```php
class WordCountTransformer extends Transformer
{
    public function transform(): void
    {
        $wordCount = str_word_count(strip_tags($this->urlContent));
        
        $this->transformationResult->result =  $wordCount;
    }
}
```
