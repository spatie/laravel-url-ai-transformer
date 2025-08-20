<?php

namespace Spatie\LaravelUrlAiTransformer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelUrlAiTransformer\Commands\LaravelUrlAiTransformerCommand;
use Spatie\LaravelUrlAiTransformer\Commands\TransformUrlsCommand;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;

class UrlAiTransformerServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->app->scoped(RegisteredTransformations::class);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-url-ai-transformer')
            ->hasConfigFile()
            ->hasMigration('create_laravel_url_ai_transformer_table')
            ->hasCommand(TransformUrlsCommand::class);
    }
}
