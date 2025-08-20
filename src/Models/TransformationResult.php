<?php

namespace Spatie\LaravelUrlAiTransformer\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;

class TransformationResult extends Model
{
    protected $casts = [
        'result' => 'array',
    ];

    public function findOrCreateForRegistration(
        string $url,
        TransformationRegistration $transformationRegistration
    ): self {
        return self::firstOrCreate([
            'url' => $url,
        ]);
    }
}
