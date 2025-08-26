---
title: Regenerating results
weight: 5
---

Sometimes you need to regenerate transformation results - perhaps the content has changed, you've updated your transformer logic, or a previous transformation failed. The `TransformationResult` model provides two methods to handle this.

## The regenerate method

The `regenerate()` method re-runs a transformation for a specific result by dispatching it to the queue:

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

$result = TransformationResult::where('url', 'https://example.com/blog/my-post')
    ->where('type', 'ldJson')
    ->first();

// Queue the regeneration
$result->regenerate();
```

## The regenerateNow method

For immediate regeneration without using the queue, use `regenerateNow()`:

```php
// Regenerate immediately, blocking until complete
$result->regenerateNow();
```
