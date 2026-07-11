---
title: Testing transformers
weight: 13
---

Testing transformers is fairly straightforward.

Here's a quick example. We use Laravel's `Http` facade to fake the fetched URL, and, because every transformer is a Laravel AI agent, we call `fake()` on the transformer to fake the AI response.

```php
use App\Transformers\SummaryTransformer;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Illuminate\Support\Facades\Http;

it('generates summaries for articles', function () {
    Http::fake([
        'https://example.com/article' => Http::response('<html><body>Test article content</body></html>'),
    ]);

    SummaryTransformer::fake([
        "• First point\n• Second point\n• Third point",
    ]);

    Transform::urls('https://example.com/article')
        ->usingTransformers(new SummaryTransformer);

    $this->artisan('transform-urls --now');

    $summary = TransformationResult::forUrl(
        'https://example.com/article',
        'summary'
    );

    expect($summary)->not->toBeNull();
    expect($summary)->toContain('•'); // Bullet points
});
```
