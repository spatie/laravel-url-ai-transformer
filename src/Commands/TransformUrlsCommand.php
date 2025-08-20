<?php

namespace Spatie\LaravelUrlAiTransformer\Commands;

use Illuminate\Console\Command;
use Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;

class TransformUrlsCommand extends Command
{
    protected $signature = 'laravel-url-ai-transformer';

    public function handle()
    {
        collect(RegisteredTransformations::all())
            ->each(function (TransformationRegistration $registration) {
                $this->processRegistration($registration);
            });
    }

    protected function processRegistration(TransformationRegistration $registration): void
    {
        /**
         * @var ProcessRegistrationAction $processRegistrationAction
         */
        $processRegistrationAction = Config::getAction('process_registration');

        $processRegistrationAction->execute($registration);
    }
}
