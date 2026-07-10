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

The provider and model come from the config file. To change them, update `config/url-ai-transformer.php` or publish a new config profile. For detailed information about the available providers, models, and options, check out the [Laravel AI documentation](https://github.com/laravel/ai).

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
