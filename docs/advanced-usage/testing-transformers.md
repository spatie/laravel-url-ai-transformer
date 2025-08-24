---
title: Testing transformers
weight: 4
---

Testing transformers is fairly straightforward.

Here's a quick example. Note that we can use Laravel's `Http` facade to fake the response of the external API.

```php
use App\Transformers\SummaryTransformer;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Illuminate\Support\Facades\Http;

it('generates summaries for articles', function () {
    Http::fake([
        'https://example.com/article' => Http::response('<html><body>Test article content</body></html>'),
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
