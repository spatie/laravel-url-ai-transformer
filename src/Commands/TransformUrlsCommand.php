<?php

namespace Spatie\LaravelUrlAiTransformer\Commands;

use Illuminate\Console\Command;
use Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;

class TransformUrlsCommand extends Command
{
    protected $signature = 'transform-urls
            {--url= : Filter transformations by URL (supports * wildcard)}
            {--transformer= : Filter transformations by transformer type (supports * wildcard)}
            {--force : Force the operation even when transformer shouldRun returns false}
            {--now : Run the transformations immediately, instead of using queued jobs}
    ';

    protected $description = 'Transform all registered URLs using their transformers';

    public function handle(): void
    {
        $this->info('Transforming URLs...');

        $urlFilter = $this->option('url');
        $transformerFilter = $this->option('transformer');
        $force = (bool) $this->option('force');
        $now = (bool) $this->option('now');

        $processRegistrationAction = Config::getAction('process_registration', ProcessRegistrationAction::class);

        $dispatchedJobCount = app(RegisteredTransformations::class)
            ->all()
            ->sum(fn (TransformationRegistration $registration) => $processRegistrationAction->execute($registration, $urlFilter, $transformerFilter, $force, $now));

        $this->comment($now
            ? "Ran {$dispatchedJobCount} transformer job(s)."
            : "Dispatched {$dispatchedJobCount} transformer job(s) to the queue.");
    }
}
