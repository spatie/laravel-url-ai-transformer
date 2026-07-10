<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport;

use Dotenv\Dotenv;
use Laravel\Ai\AiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelUrlAiTransformer\UrlAiTransformerServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        // Load test environment variables before parent setup
        if (file_exists(__DIR__.'/../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__.'/..');
            $dotenv->load();
        }

        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            AiServiceProvider::class,
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

        config()->set('queue.default', 'sync');

        $migration = include __DIR__.'/../../database/migrations/create_url_ai_transformer_table.php.stub';
        $migration->up();

        config()->set('ai.providers.openai.key', env('OPENAI_API_KEY'));
    }
}
