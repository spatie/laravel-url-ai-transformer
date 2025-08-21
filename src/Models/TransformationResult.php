<?php

namespace Spatie\LaravelUrlAiTransformer\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;

class TransformationResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'result' => 'array',
    ];

    public static function findOrCreateForRegistration(
        string $url,
        TransformationRegistration $transformationRegistration
    ): self {
        return self::firstOrCreate([
            'url' => $url,
            'type' => $transformationRegistration->getType(),
        ], [
            'result' => [],
        ]);
    }

    public static function forUrl(string $url): ?self
    {
        return self::where('url', $url)->first();
    }

    public function setResult(string $key, mixed $value): self
    {
        $results = $this->result ?? [];

        $results[$key] = $value;

        $this->result = $results;

        return $this;
    }

    public function result(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->result;
        }

        return $this->result[$key] ?? null;
    }
}
