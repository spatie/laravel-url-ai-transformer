<?php

namespace Spatie\LaravelUrlAiTransformer\Tests\TestSupport\Models;

use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

class CustomTransformationResult extends TransformationResult
{
    protected $table = 'transformation_results';
}
