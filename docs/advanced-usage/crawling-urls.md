---
title: Crawling URLs
weight: 5
---

Instead of manually registering URLs, you can use the [spatie/crawler](https://github.com/spatie/crawler) package to crawl (parts of) your website.

Install it first:

```bash
composer require spatie/crawler
```

Here's an example of how to crawl all internal URLs of your website and pass them to the `Transform` class. Notice that we pass a closure to the `urls` method. By using a closure, the crawling will only happen when we actually perform the transformation, and not on each request.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Crawler\CrawledUrl;
use Spatie\Crawler\Crawler;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

class AiTransformerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Transform::urls(
            fn () => $this->crawlAllUrls(),
        )->usingTransformers(new LdJsonTransformer);
    }

    protected function crawlAllUrls(): array
    {
        $crawledUrls = Crawler::create(url('/'))
            ->internalOnly()
            ->foundUrls();

        return array_map(
            fn (CrawledUrl $crawledUrl): string => $crawledUrl->url,
            $crawledUrls,
        );
    }
}
```

`foundUrls()` returns `CrawledUrl` objects, while `Transform::urls()` expects URL strings. The `array_map()` converts each result to its URL.

For more information on how to use the `Crawler` class, check out [the spatie/crawler docs](https://github.com/spatie/crawler).
