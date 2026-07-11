<?php

namespace Spatie\LaravelUrlAiTransformer\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeTransformerCommand extends GeneratorCommand
{
    protected $name = 'make:transformer';

    protected $description = 'Create a new transformer class';

    protected $type = 'Transformer';

    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/transformer.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return "{$rootNamespace}\\Transformers";
    }
}
