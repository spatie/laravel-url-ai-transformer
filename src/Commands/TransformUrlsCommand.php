<?php

namespace Spatie\LaravelUrlAiTransformer\Commands;

use Illuminate\Console\Command;
use Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;

class TransformUrlsCommand extends Command
{
    protected $signature = 'laravel-url-ai-transformer
            {--url= : Filter transformations by URL (supports * wildcard)}
            {--transformer= : Filter transformations by transformer type (supports * wildcard)}
    ';

    public function handle(): void
    {
        $urlFilter = $this->option('url');
        $transformerFilter = $this->option('transformer');

        app(RegisteredTransformations::class)
            ->all()
            ->each(function (TransformationRegistration $registration) use ($urlFilter, $transformerFilter) {
                $this->processRegistration($registration, $urlFilter, $transformerFilter);
            });
    }

    protected function processRegistration(TransformationRegistration $registration, ?string $urlFilter, ?string $transformerFilter): void
    {
        /**
         * @var ProcessRegistrationAction $processRegistrationAction
         */
        $processRegistrationAction = Config::getAction('process_registration', ProcessRegistrationAction::class);

        $processRegistrationAction->execute($registration, $urlFilter, $transformerFilter);
    }
}
