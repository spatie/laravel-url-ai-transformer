---
title: Customizing AI models
weight: 3
---

By default, transformers use the AI provider and model configured in your `config/url-ai-transformer.php` file. However, you can customize which AI models to use on a per-transformer basis, or even skip AI entirely.

This package uses [Prism](https://prismphp.com) under the hood for all AI interactions. Prism provides a unified interface for working with different AI providers like OpenAI, Anthropic, Google Gemini, and more. For detailed information about available providers, models, and configuration options, check out the [Prism documentation](https://prismphp.com/docs).

Here's an example where we use various options like temperature and max tokens.

```php
class PreciseTransformer extends Transformer
{
    public function transform(): void
    {
        $response = Prism::text()
            ->using(Config::aiProvider(), Config::aiModel())
            ->withPrompt($this->getPrompt())
            ->withMaxTokens(1000)
            ->withTemperature(0.1) // Low temperature for precise, consistent output
            ->asText();

        $this->transformationResult->result = $response->text;
    }
}

class CreativeTransformer extends Transformer
{
    public function transform(): void
    {
        $response = Prism::text()
            ->using(Config::aiProvider(), Config::aiModel())
            ->withPrompt($this->getPrompt())
            ->withMaxTokens(2000)
            ->withTemperature(0.8) // Higher temperature for creative output
            ->asText();

        $this->transformationResult->result = $response->text;
    }
}
```

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
