---
title: Testing transformers
weight: 13
---

Testing transformers is fairly straightforward.

Here's a quick example. We use Laravel's `Http` facade to fake the fetched URL, and, because every transformer is a Laravel AI agent, we call `fake()` on the transformer to fake the AI response.

```php
use App\Transformers\SummaryTransformer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;

it('generates summaries for articles', function () {
    Http::fake([
        'https://example.com/article' => Http::response('<html><body>Test article content</body></html>'),
    ]);

    SummaryTransformer::fake([
        "• First point\n• Second point\n• Third point",
    ]);

    Transform::urls('https://example.com/article')
        ->usingTransformers(new SummaryTransformer);

    Artisan::call('transform-urls', ['--now' => true]);

    $summary = TransformationResult::forUrl(
        'https://example.com/article',
        'summary'
    );

    expect($summary)->not->toBeNull();
    expect($summary)->toContain('•');
});
```
