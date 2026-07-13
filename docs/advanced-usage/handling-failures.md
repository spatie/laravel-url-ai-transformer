---
title: Handling failures
weight: 8
---

The package tracks transformation failures automatically. When a transformer throws, the exception details are stored on the transformation result, and the `TransformerFailed` event is fired.

## Inspecting failures

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

$transformationResult = TransformationResult::findForUrl('https://example.com', 'ldJson');

if ($transformationResult->latest_exception_seen_at) {
    $transformationResult->latest_exception_message; // the error message
    $transformationResult->latest_exception_trace; // the stack trace
}
```

The `latest_exception` fields are cleared automatically when the transformation completes successfully.

## Retrying a failed transformation

```php
$transformationResult->clearException(persist: true);

$transformationResult->regenerate();
```

## Getting notified of failures

Listen for the `TransformerFailed` event to alert your team or add custom retry logic. See [Listening for events](./events).
