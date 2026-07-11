---
title: Customizing the job
weight: 11
---

Each transformation runs in a queued `ProcessTransformerJob`. You can extend it to configure retries, backoff, middleware, or anything else a Laravel job supports:

```php
// app/Jobs/CustomProcessTransformerJob.php
namespace App\Jobs;

use Illuminate\Queue\Middleware\RateLimited;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;

class CustomProcessTransformerJob extends ProcessTransformerJob
{
    public $tries = 5;

    public $backoff = [60, 120, 300];

    public function middleware(): array
    {
        return [
            new RateLimited('transformations'),
        ];
    }
}
```

Register it in the config file:

```php
// config/url-ai-transformer.php
'process_transformer_job' => App\Jobs\CustomProcessTransformerJob::class,
```
