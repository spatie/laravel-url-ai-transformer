<?php

namespace Spatie\LaravelUrlAiTransformer\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelUrlAiTransformer\Support\TransformationRegistration;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformationResult extends Model
{
    protected $guarded = [];

    public static function findOrCreateForRegistration(
        string $url,
        Transformer $transformer,
    ): self {
        return self::firstOrCreate([
            'url' => $url,
            'type' => $transformer->type(),
        ]);
    }

    public static function forUrl(string $url, string $type): ?string
    {
        return self::query()
            ->where('url', $url)
            ->where('type', $type)
            ->first()?->result;
    }
}
