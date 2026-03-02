---
title: Crawling URLs
weight: 2
---

Instead of manually registering URLs, you can use the [spatie/crawler](https://github.com/spatie/crawler) package to crawl (parts of) your website.

Here's an example of how to crawl all internal URLs of your website and pass them to the `Transform` class. Notice that we pass a closure to the `urls` method. By using a closure, the crawling will only happen when we actually perform the transformation, and not on each request.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Crawler\Crawler;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

class AiTransformerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Transform::urls(
            fn() => $this->crawlAllUrls()
        )->usingTransformers(new LdJsonTransformer());
    }

    protected function crawlAllUrls(): array
    {
        return Crawler::create(url('/'))
            ->internalOnly()
            ->foundUrls();
    }
}
```

For more information on how to use the `Crawler` class, check out [the spatie/crawler docs](https://github.com/spatie/crawler).
