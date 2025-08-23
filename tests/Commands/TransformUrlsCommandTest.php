<?php

use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\TestTransformer;
use Spatie\LaravelUrlAiTransformer\Transformers\ImageTransformer;

it('can transform an URL', function () {
    Transform::urls('https://spatie.be')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    expect(TransformationResult::forUrl('https://spatie.be', 'ld'))->toBe('dummy result');
});

it('can transform a webpage to an image', function () {
    Transform::urls('https://spatie.be/blog/how-to-make-your-ai-agent-program-with-grace-and-style')->usingTransformers(new ImageTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

})->skip();

it('can filter transformations by exact URL', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Content 1</body></html>', 200),
        'https://spatie.be' => Http::response('<html><body>Content 2</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);
    Transform::urls('https://spatie.be')->usingTransformers(new TestTransformer);

    $this
        ->artisan(TransformUrlsCommand::class, ['--url' => 'https://example.com'])
        ->assertSuccessful();

    // Only the matching URL should be processed
    expect(TransformationResult::forUrl('https://example.com', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://spatie.be', 'Test'))->toBeNull();
});

it('can filter transformations using URL wildcards', function () {
    Http::fake([
        'https://spatie.be/blog/post-1' => Http::response('<html><body>Post 1</body></html>', 200),
        'https://spatie.be/blog/post-2' => Http::response('<html><body>Post 2</body></html>', 200),
        'https://example.com/page' => Http::response('<html><body>Page</body></html>', 200),
    ]);

    Transform::urls('https://spatie.be/blog/post-1')->usingTransformers(new DummyLdTransformer);
    Transform::urls('https://spatie.be/blog/post-2')->usingTransformers(new TestTransformer);
    Transform::urls('https://example.com/page')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class, ['--url' => 'https://spatie.be/blog/*'])
        ->assertSuccessful();

    // Only URLs matching the wildcard should be processed
    expect(TransformationResult::forUrl('https://spatie.be/blog/post-1', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://spatie.be/blog/post-2', 'Test'))->toBe('test');
    expect(TransformationResult::forUrl('https://example.com/page', 'ld'))->toBeNull();
});

it('can filter transformations by exact transformer type', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class, ['--transformer' => 'ld'])
        ->assertSuccessful();

    // Only the ld transformer should be processed
    expect(TransformationResult::forUrl('https://example.com', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://example.com', 'Test'))->toBeNull();
});

it('can filter transformations using transformer wildcards', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class, ['--transformer' => 'T*'])
        ->assertSuccessful();

    // Only the Test transformer should be processed
    expect(TransformationResult::forUrl('https://example.com', 'ld'))->toBeNull();
    expect(TransformationResult::forUrl('https://example.com', 'Test'))->toBe('test');
});

it('can combine URL and transformer filters', function () {
    Http::fake([
        'https://spatie.be/blog/post-1' => Http::response('<html><body>Post 1</body></html>', 200),
        'https://spatie.be/blog/post-2' => Http::response('<html><body>Post 2</body></html>', 200),
        'https://example.com/page' => Http::response('<html><body>Page</body></html>', 200),
    ]);

    Transform::urls('https://spatie.be/blog/post-1')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );
    Transform::urls('https://spatie.be/blog/post-2')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );
    Transform::urls('https://example.com/page')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class, [
            '--url' => 'https://spatie.be/blog/*',
            '--transformer' => 'ld',
        ])
        ->assertSuccessful();

    // Only blog URLs with ld transformer should be processed
    expect(TransformationResult::forUrl('https://spatie.be/blog/post-1', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://spatie.be/blog/post-2', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://spatie.be/blog/post-1', 'Test'))->toBeNull();
    expect(TransformationResult::forUrl('https://spatie.be/blog/post-2', 'Test'))->toBeNull();
    expect(TransformationResult::forUrl('https://example.com/page', 'ld'))->toBeNull();
    expect(TransformationResult::forUrl('https://example.com/page', 'Test'))->toBeNull();
});

it('processes all transformations when no filters are provided', function () {
    Http::fake([
        'https://example.com' => Http::response('<html><body>Content 1</body></html>', 200),
        'https://spatie.be' => Http::response('<html><body>Content 2</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);
    Transform::urls('https://spatie.be')->usingTransformers(new TestTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    // All transformations should be processed
    expect(TransformationResult::forUrl('https://example.com', 'ld'))->toBe('dummy result');
    expect(TransformationResult::forUrl('https://spatie.be', 'Test'))->toBe('test');
});
