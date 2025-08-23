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
    ';

    public function handle(): void
    {
        $urlFilter = $this->option('url');
        $transformerFilter = $this->option('transformer');
        $force = (bool)$this->option('force');

        app(RegisteredTransformations::class)
            ->all()
            ->each(function (TransformationRegistration $registration) use ($urlFilter, $transformerFilter, $force) {
                $this->processRegistration($registration, $urlFilter, $transformerFilter, $force);
            });
    }

    protected function processRegistration(
        TransformationRegistration $registration,
        ?string $urlFilter,
        ?string $transformerFilter,
        bool $force
    ): void
    {
        /**
         * @var ProcessRegistrationAction $processRegistrationAction
         */
        $processRegistrationAction = Config::getAction('process_registration', ProcessRegistrationAction::class);

        $processRegistrationAction->execute($registration, $urlFilter, $transformerFilter, $force);
    }
}
