---
title: Advanced usage
weight: 2
---

This section covers advanced features and customization options for power users.

## Key topics

- [Overriding actions](./overriding-actions) - Customize how URLs are fetched and processed
- [Customizing AI models](./customizing-ai-models) - Use different AI providers, models, and parameters per transformer
- [Testing transformers](./testing-transformers) - Comprehensive testing strategies for your custom transformers

## Advanced patterns

### Regenerating transformations

Sometimes you need to regenerate transformations for specific URLs:

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

// Find an existing transformation
$result = TransformationResult::where('url', 'https://example.com')
    ->where('type', 'ldJson')
    ->first();

// Regenerate it (queued)
$result->regenerate();

// Regenerate it immediately
$result->regenerateNow();
```

### Handling failures

The package tracks transformation failures automatically:

```php
$result = TransformationResult::first();

// Check if transformation has failed
if ($result->latest_exception_seen_at) {
    echo "Failed at: " . $result->latest_exception_seen_at;
    echo "Error: " . $result->latest_exception_message;
}

// Clear the error and try again
$result->clearException(persist: true);
$result->regenerate();
```

### Custom job handling

You can customize the job that processes transformations:

```php
// app/Jobs/CustomProcessTransformerJob.php
namespace App\Jobs;

use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;

class CustomProcessTransformerJob extends ProcessTransformerJob
{
    public $tries = 5;
    public $backoff = [60, 120, 300]; // Exponential backoff
    
    public function middleware(): array
    {
        return [
            new RateLimited('transformations'),
        ];
    }
}
```

Register it in the config:

```php
'process_transformer_job' => App\Jobs\CustomProcessTransformerJob::class,
```

### Filtering transformations

The command supports powerful filtering options:

```bash
# Transform only blog posts
php artisan transform-urls --url="*/blog/*"

# Transform only with specific transformer
php artisan transform-urls --transformer="ldJson"

# Combine filters
php artisan transform-urls --url="https://example.com/*" --transformer="image*"

# Force transformation even if shouldRun returns false
php artisan transform-urls --force

# Run synchronously for debugging
php artisan transform-urls --now
```

### Events and listeners

The package fires events during transformation:

```php
use Spatie\LaravelUrlAiTransformer\Events\TransformerStarted;
use Spatie\LaravelUrlAiTransformer\Events\TransformerEnded;
use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;

// In EventServiceProvider
protected $listen = [
    TransformerStarted::class => [
        LogTransformationStart::class,
    ],
    TransformerEnded::class => [
        NotifyTransformationComplete::class,
        UpdateSearchIndex::class,
    ],
    TransformerFailed::class => [
        AlertAdministrators::class,
        RetryFailedTransformation::class,
    ],
];
```

### Extending the model

Create your own model with additional functionality:

```php
// app/Models/TransformationResult.php
namespace App\Models;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult as BaseModel;

class TransformationResult extends BaseModel
{
    // Add custom scopes
    public function scopeSuccessful($query)
    {
        return $query->whereNotNull('successfully_completed_at');
    }
    
    public function scopeFailed($query)
    {
        return $query->whereNotNull('latest_exception_seen_at');
    }
    
    // Add custom methods
    public function getStructuredData(): array
    {
        return json_decode($this->result, true) ?? [];
    }
    
    public function isStale(): bool
    {
        return $this->successfully_completed_at < now()->subDays(7);
    }
}
```

Register it in the config:

```php
'model' => App\Models\TransformationResult::class,
```

### Performance optimization

For large-scale transformations:

1. **Use queues**: Transformations run in background jobs by default
2. **Batch processing**: Process URLs in chunks
3. **Rate limiting**: Prevent overwhelming external services
4. **Caching**: Cache fetched content to avoid redundant requests

```php
// Process in batches
$urls = collect($thousandsOfUrls)->chunk(100);

foreach ($urls as $batch) {
    Transform::urls(...$batch->toArray())
        ->usingTransformers(new LdJsonTransformer);
}
```

### Database optimization

Add indexes for better query performance:

```php
Schema::table('transformation_results', function (Blueprint $table) {
    $table->index(['url', 'type']);
    $table->index('successfully_completed_at');
    $table->index('latest_exception_seen_at');
});
```

