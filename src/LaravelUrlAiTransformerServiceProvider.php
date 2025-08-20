<?php

namespace Spatie\LaravelUrlAiTransformer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelUrlAiTransformer\Commands\LaravelUrlAiTransformerCommand;

class LaravelUrlAiTransformerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-url-ai-transformer')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_url_ai_transformer_table')
            ->hasCommand(LaravelUrlAiTransformerCommand::class);
    }
}
