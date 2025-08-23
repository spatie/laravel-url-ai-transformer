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
            {--force= : Force the operation even when transformer shouldRun returns false}
            {--now= : Dispatch jobs immediately, instead of using a queued job}
    ';

    public function handle(): void
    {
        $this->info('Dispatching jobs to transform URLs...');

        $urlFilter = $this->option('url');
        $transformerFilter = $this->option('transformer');
        $force = (bool)$this->option('force');
        $now = (bool)$this->option('now');

        app(RegisteredTransformations::class)
            ->all()
            ->each(fn(TransformationRegistration $registration) =>
                $this->processRegistration($registration, $urlFilter, $transformerFilter, $force, $now)
            );

        $this->info('All done!');
    }

    protected function processRegistration(
        TransformationRegistration $registration,
        ?string $urlFilter,
        ?string $transformerFilter,
        bool $force,
        bool $now,
    ): void
    {
        $processRegistrationAction = Config::getAction('process_registration', ProcessRegistrationAction::class);

        $processRegistrationAction->execute($registration, $urlFilter, $transformerFilter, $force, $now);
    }
}
