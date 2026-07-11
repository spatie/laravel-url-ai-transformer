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
