---
title: Conditional transformations
weight: 4
---

Sometimes you don't want transformers to run every time the command is executed. The `shouldRun()` method allows you to add conditions that determine when a transformer should process content.

## Basic conditional logic

You can add an optional `shouldRun` method to your transformer. If `shouldRun` returns `false`, the transformer is skipped:

```php
class MonthlyReportTransformer extends Transformer
{
    public function shouldRun(): bool
    {
        $completedAt = $this->transformationResult->successfully_completed_at;

        if (! $completedAt) {
            return true;
        }

        return $completedAt->diffInDays() > 30;
    }

    public function instructions(): Stringable|string
    {
        return 'Generate a monthly report summary...';
    }
}
```

This example only runs the transformation when it has never completed before, or when the last successful run was more than 30 days ago.

You can force skipped transformers to run anyway with the `--force` option:

```bash
php artisan transform-urls --force
```
