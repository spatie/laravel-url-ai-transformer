---
title: Listening for events
weight: 9
---

The package fires events during the transformation lifecycle:

- `Spatie\LaravelUrlAiTransformer\Events\TransformerStarted`: fired right before a transformer runs
- `Spatie\LaravelUrlAiTransformer\Events\TransformerEnded`: fired after a transformer ran successfully
- `Spatie\LaravelUrlAiTransformer\Events\TransformerFailed`: fired when a transformer throws

Each event exposes the `$transformer` and `$transformationResult` as public properties. `TransformerStarted` and `TransformerEnded` also expose the `$url` and `$urlContent`. `TransformerFailed` exposes the thrown `$exception`.

You can listen for these events like any other Laravel event. Laravel discovers listeners automatically, so type-hinting the event in a listener's `handle` method is enough:

```php
namespace App\Listeners;

use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;

class AlertAdministrators
{
    public function handle(TransformerFailed $event): void
    {
        // notify your team using $event->exception, $event->transformationResult, ...
    }
}
```

Alternatively, register a closure-based listener in a service provider:

```php
use Illuminate\Support\Facades\Event;
use Spatie\LaravelUrlAiTransformer\Events\TransformerEnded;

Event::listen(function (TransformerEnded $event) {
    // update a search index, clear a cache, ...
});
```
