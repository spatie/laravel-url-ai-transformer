---
title: Regenerating results
weight: 7
---

Sometimes you need to regenerate transformation results, perhaps because the content has changed, you've updated your transformer logic, or a previous transformation failed. The `TransformationResult` model provides two methods to handle this.

## The regenerate method

The `regenerate()` method re-runs a transformation for a specific result by dispatching it to the queue:

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

$transformationResult = TransformationResult::findForUrl('https://example.com/blog/my-post', 'ldJson');

$transformationResult->regenerate();
```

## The regenerateNow method

For immediate regeneration without using the queue, use `regenerateNow()`:

```php
$transformationResult->regenerateNow();
```
