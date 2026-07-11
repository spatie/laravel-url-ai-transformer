<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Actions;

use Spatie\LaravelUrlAiTransformer\Actions\PrepareUrlContentAction;

class UppercasePrepareUrlContentAction extends PrepareUrlContentAction
{
    public function execute(string $urlContent): string
    {
        return strtoupper(parent::execute($urlContent));
    }
}
