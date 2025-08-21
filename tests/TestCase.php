<?php

namespace Spatie\LaravelUrlAiTransformer\Tests;

use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelUrlAiTransformer\UrlAiTransformerServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        // Load test environment variables before parent setup
        if (file_exists(__DIR__ . '/.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();
        }

        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            UrlAiTransformerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $migrationClass = include_once __DIR__.'/../database/migrations/create_url_ai_transformer_table.php.stub';
        $migrationClass->up();

        config()->set('prism.providers.openai.api_key', env('OPENAI_API_KEY'));
        config()->set('prism.providers.openai.url', env('OPENAI_API_URL', 'https://api.openai.com/v1'));
    }
}
