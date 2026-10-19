<?php

use Spatie\LaravelUrlAiTransformer\Actions\PrepareUrlContentAction;

it('removes very large inline script and style blocks', function (string $tag) {
    $content = str_repeat('if (a < b) { run(); } ', 100_000);

    $html = "<html><head><{$tag} id=\"data\">{$content}</{$tag}></head><body><p>Real page text</p></body></html>";

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Real page text');
})->with(['script', 'style']);

it('removes a very large unclosed block up to the end of the content', function (string $tag) {
    $content = str_repeat('if (a < b) { run(); } ', 100_000);

    $html = "<html><body><p>Real page text</p><{$tag}>{$content}";

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Real page text');
})->with(['script', 'style']);

it('recognizes closing tags with whitespace before the bracket', function () {
    $html = "<h1>Title</h1><script>var a = 1;</script ><p>First</p><style>p {}</style\n><p>Second</p>";

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Title First Second');
});

it('matches closing tags case insensitively without accepting longer tag names', function () {
    $html = '<p>Before</p><ScRiPt>ignore </scripture> still hidden</sCrIpT><p>Between</p><STYLE>ignore </stylesheet> still hidden</style><p>After</p>';

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Before Between After');
});

it('preserves text and similarly named elements outside blocks', function () {
    $html = '<p>Hello &amp; friends</p><scripture>Keep this</scripture><stylesheet>And this</stylesheet>';

    expect((new PrepareUrlContentAction)->execute($html))->toBe('Hello & friends Keep this And this');
});
