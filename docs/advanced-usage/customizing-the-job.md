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
    public int $tries = 5;

    public array $backoff = [60, 120, 300];

    public function middleware(): array
    {
        return [
            new RateLimited('transformations'),
        ];
    }
}
```

The package records the exception and fires `TransformerFailed` before rethrowing it. Laravel can therefore retry the job according to your `$tries` and `$backoff` settings. Because the event is fired for every failed attempt, listeners should be safe to run more than once.

The `RateLimited` middleware expects a limiter with the same name. Register it in a service provider:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('transformations', fn (object $job) => Limit::perMinute(10));
```

Register it in the config file:

```php
// config/url-ai-transformer.php
'process_transformer_job' => App\Jobs\CustomProcessTransformerJob::class,
```

These job settings apply to the transformation and AI request. The `transform-urls` command fetches each URL before dispatching its transformation jobs.
