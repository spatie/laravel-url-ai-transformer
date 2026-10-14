<?php

use Spatie\LaravelUrlAiTransformer\Actions\PrepareUrlContentAction;

it('removes very large inline scripts', function () {
    $script = str_repeat('window.__DATA__ = {"key": "value"};', 60_000);

    $html = "<html><head><script id=\"__NEXT_DATA__\">{$script}</script></head><body><p>Real page text</p></body></html>";

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Real page text');
});

it('removes a very large unclosed inline script up to the end of the content', function () {
    $script = str_repeat('if (a < b) { run(); } ', 100_000);

    $html = "<html><body><p>Real page text</p><script>{$script}";

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Real page text');
});

it('recognizes closing tags with whitespace before the bracket', function () {
    $html = "<h1>Title</h1><script>var a = 1;</script ><p>First</p><style>p {}</style\n><p>Second</p>";

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Title First Second');
});
