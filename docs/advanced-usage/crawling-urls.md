---
title: Crawling URLs
weight: 2
---

Instead of manually registering URLs, you can use the [spatie/crawler](https://github.com/spatie/crawler) package to crawl (parts of) your website.

Here's an example of how to crawl all URLs of your website and pass it to the `Transform` class. Notice that we pass a closure to the `urls` method. By using a closure, the crawling will only happen when we actually perform the transformation, and not on each request.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
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
        $startUrl = url('/');

        $urls = [];

        Crawler::create()
            ->setCrawlObserver(new class($urls) extends CrawlObserver
            {
                public function __construct(private array &$urls) {}

                public function crawled($url, $response, $foundOnUrl = null, ?string $linkText = null): void
                {
                    $this->urls[] = (string) $url;
                }
            })
            ->setCrawlProfile(new CrawlInternalUrls($startUrl))
            ->startCrawling($startUrl);

        return array_unique($urls);
    }
}
```

For more information on how to use the `Crawler` class, check out [the spatie/crawler docs](https://github.com/spatie/crawler).
