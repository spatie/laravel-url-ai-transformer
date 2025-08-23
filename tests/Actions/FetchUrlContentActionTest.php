<?php

use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction;

it('can fetch URL content', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    $action = new FetchUrlContentAction;
    $content = $action->execute('https://example.com');

    expect($content)->toBe('<html><body>Test content</body></html>');
});

it('throws exception on HTTP error', function () {
    Http::fake([
        'https://example.com' => Http::response('Not Found', 404),
    ]);

    $action = new FetchUrlContentAction;

    expect(fn () => $action->execute('https://example.com'))
        ->toThrow(\Illuminate\Http\Client\RequestException::class);
});

it('can be overridden with custom implementation', function () {
    // Create a custom implementation
    $customAction = new class extends FetchUrlContentAction
    {
        public function execute(string $url): string
        {
            return 'custom content for '.$url;
        }
    };

    $content = $customAction->execute('https://example.com');

    expect($content)->toBe('custom content for https://example.com');
});

it('is used by ProcessRegistrationAction via config', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Config test</body></html>', 200),
    ]);

    // The ProcessRegistrationAction should use the configured action
    $processAction = new \Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction;

    // Use reflection to test the protected method
    $reflection = new ReflectionClass($processAction);
    $method = $reflection->getMethod('fetchUrlContent');
    $method->setAccessible(true);

    $content = $method->invoke($processAction, 'https://example.com');

    expect($content)->toBe('<html><body>Config test</body></html>');
});

it('can handle large content', function () {
    $largeContent = str_repeat('Lorem ipsum dolor sit amet. ', 10000);

    Http::fake([
        'https://example.com' => Http::response($largeContent, 200),
    ]);

    $action = new FetchUrlContentAction;
    $content = $action->execute('https://example.com');

    expect($content)->toBe($largeContent);
    expect(strlen($content))->toBeGreaterThan(100000);
});
