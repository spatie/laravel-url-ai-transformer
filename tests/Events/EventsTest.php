<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Events\TransformerEnded;
use Spatie\LaravelUrlAiTransformer\Events\TransformerFailed;
use Spatie\LaravelUrlAiTransformer\Events\TransformerStarted;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\DummyLdTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\FailingTransformer;
use Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Transformers\TestTransformer;

it('dispatches TransformerStarted and TransformerEnded events', function () {
    Event::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(new DummyLdTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    Event::assertDispatched(TransformerStarted::class, function ($event) {
        return $event->transformer instanceof DummyLdTransformer
            && $event->transformationResult instanceof TransformationResult
            && $event->url === 'https://example.com'
            && str_contains($event->urlContent, 'Test content');
    });

    Event::assertDispatched(TransformerEnded::class, function ($event) {
        return $event->transformer instanceof DummyLdTransformer
            && $event->transformationResult instanceof TransformationResult
            && $event->url === 'https://example.com'
            && str_contains($event->urlContent, 'Test content');
    });
});

it('dispatches events for multiple transformers', function () {
    Event::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(
        new DummyLdTransformer,
        new TestTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    Event::assertDispatchedTimes(TransformerStarted::class, 2);
    Event::assertDispatchedTimes(TransformerEnded::class, 2);

    Event::assertDispatched(TransformerStarted::class, function ($event) {
        return $event->transformer instanceof DummyLdTransformer;
    });

    Event::assertDispatched(TransformerStarted::class, function ($event) {
        return $event->transformer instanceof TestTransformer;
    });
});

it('does not dispatch events for transformers that should not run', function () {
    Event::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    $skippableTransformer = new class extends \Spatie\LaravelUrlAiTransformer\Transformers\Transformer
    {
        public function transform(): void
        {
            $this->transformationResult->result = 'skipped result';
        }

        public function getPrompt(): string
        {
            return 'skipped prompt';
        }

        public function shouldRun(): bool
        {
            return false;
        }
    };

    Transform::urls('https://example.com')->usingTransformers($skippableTransformer);

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    Event::assertNotDispatched(TransformerStarted::class);
    Event::assertNotDispatched(TransformerEnded::class);
});

it('dispatches TransformerFailed event when transformer throws exception', function () {
    Event::fake();

    Http::fake([
        'https://example.com' => Http::response('<html><body>Test content</body></html>', 200),
    ]);

    Transform::urls('https://example.com')->usingTransformers(
        new DummyLdTransformer,
        new FailingTransformer
    );

    $this
        ->artisan(TransformUrlsCommand::class)
        ->assertSuccessful();

    // Successful transformer should have started and ended events
    Event::assertDispatched(TransformerStarted::class, function ($event) {
        return $event->transformer instanceof DummyLdTransformer;
    });

    Event::assertDispatched(TransformerEnded::class, function ($event) {
        return $event->transformer instanceof DummyLdTransformer;
    });

    // Failed transformer should have started and failed events, but no ended event
    Event::assertDispatched(TransformerStarted::class, function ($event) {
        return $event->transformer instanceof FailingTransformer;
    });

    Event::assertDispatched(TransformerFailed::class, function ($event) {
        return $event->transformer instanceof FailingTransformer
            && $event->transformationResult instanceof TransformationResult
            && $event->exception->getMessage() === 'Transformer failed to process content';
    });

    // Failed transformer should not have ended event
    Event::assertNotDispatched(TransformerEnded::class, function ($event) {
        return $event->transformer instanceof FailingTransformer;
    });
});
