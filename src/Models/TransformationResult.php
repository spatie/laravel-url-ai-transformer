<?php

namespace Spatie\LaravelUrlAiTransformer\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformationResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'latest_exception_seen_at' => 'datetime',
    ];

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

    public function recordException(Exception $exception): void
    {
        $this->update([
            'latest_exception_seen_at' => now(),
            'latest_exception_message' => $exception->getMessage(),
            'latest_exception_trace' => $exception->getTraceAsString(),
        ]);

    }
}
