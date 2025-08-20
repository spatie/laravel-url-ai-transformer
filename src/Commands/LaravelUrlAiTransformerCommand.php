<?php

namespace Spatie\LaravelUrlAiTransformer\Commands;

use Illuminate\Console\Command;

class LaravelUrlAiTransformerCommand extends Command
{
    public $signature = 'laravel-url-ai-transformer';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
