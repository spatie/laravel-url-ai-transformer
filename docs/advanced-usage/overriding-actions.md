---
title: Overriding actions
weight: 12
---

Actions are classes that handle the core operations of the package. By overriding them, you can customize how low-level operations are performed.

## Overriding the fetch action

Here's an example where we are going to add authentication headers when fetching certain URLs.

```php
// app/Actions/CustomFetchUrlContentAction.php
namespace App\Actions;

use Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction;
use Illuminate\Support\Facades\Http;

class CustomFetchUrlContentAction extends FetchUrlContentAction
{
    public function execute(string $url): string
    {
        if (str_contains($url, 'api.mycompany.com')) {
            return Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.internal_api.token'),
                'Accept' => 'application/json',
            ])->get($url)->body();
        }

        return parent::execute($url);
    }
}
```

Register your custom action in the config file:

```php
// config/url-ai-transformer.php
return [
    'actions' => [
        'fetch_url_content' => App\Actions\CustomFetchUrlContentAction::class,
        // ... other actions
    ],
];
```

## Overriding the prepare action

Before the fetched URL content is sent to the AI, it passes through the `PrepareUrlContentAction`. The default implementation removes scripts and styles, strips HTML tags, collapses whitespace, and limits the result to 6000 characters.

By overriding this action, you control what every transformer sends to the AI. Here's an example that keeps only the `<main>` element of a page and allows more characters:

```php
// app/Actions/PrepareMainContentAction.php
namespace App\Actions;

use Illuminate\Support\Str;
use Spatie\LaravelUrlAiTransformer\Actions\PrepareUrlContentAction;

class PrepareMainContentAction extends PrepareUrlContentAction
{
    public function execute(string $urlContent): string
    {
        if (preg_match('#<main\b[^>]*>(.*?)</main>#is', $urlContent, $matches)) {
            $urlContent = $matches[1];
        }

        return Str::limit(Str::squish(strip_tags($urlContent)), 20000);
    }
}
```

Register it in the config file:

```php
// config/url-ai-transformer.php
return [
    'actions' => [
        'prepare_url_content' => App\Actions\PrepareMainContentAction::class,
        // ... other actions
    ],
];
```

A single transformer can still take full control by overriding its `content()` method. In that case, the prepare action is not used for that transformer.
