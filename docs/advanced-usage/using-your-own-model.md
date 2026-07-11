---
title: Using your own model
weight: 10
---

You can replace the `TransformationResult` model with your own to add scopes, methods, or extra columns. Extend the package's model:

```php
// app/Models/TransformationResult.php
namespace App\Models;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult as BaseTransformationResult;

class TransformationResult extends BaseTransformationResult
{
    public function scopeSuccessful($query)
    {
        return $query->whereNotNull('successfully_completed_at');
    }

    public function scopeFailed($query)
    {
        return $query->whereNotNull('latest_exception_seen_at');
    }

    public function isStale(): bool
    {
        return $this->successfully_completed_at < now()->subDays(7);
    }
}
```

Register it in the config file:

```php
// config/url-ai-transformer.php
'model' => App\Models\TransformationResult::class,
```

The package will now use your model everywhere: when storing results, and when you retrieve them through `forUrl()` and `findForUrl()`.

If your model needs extra columns, add them with a migration in your application. A transformer can fill them by overriding `resultFrom()`. See [Customizing the stored result](./customizing-the-result).
