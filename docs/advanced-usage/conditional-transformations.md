---
title: Conditional transformations
weight: 2
---

Sometimes you don't want transformers to run every time the command is executed. The `shouldRun()` method allows you to add conditions that determine when a transformer should process content.

## Basic conditional logic

You can add an optional `shouldRun` method to your transformer. If `shouldRun` returns `false`, the transformer is skipped:

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

This example only runs the transformation if it hasn't been successfully completed in the last 30 days.
