<?php

namespace Spatie\LaravelUrlAiTransformer\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;

class TransformationResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'latest_exception_seen_at' => 'datetime',
        'successfully_completed_at' => 'datetime',
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

    public function clearException(bool $persist): self
    {
        $this->latest_exception_seen_at = null;
        $this->latest_exception_message = null;
        $this->latest_exception_trace = null;

        if ($persist) {
            $this->save();
        }

        return $this;
    }

    public function regenerate(bool $now = false): void
    {
        $transformerClass = app(RegisteredTransformations::class)->getTransformationClassForType($this->type);

        $fetchAction = Config::getAction('fetch_url_content', FetchUrlContentAction::class);

        $urlContent = $fetchAction->execute($this->url);

        $method = $now
            ? 'dispatchSync'
            : 'dispatch';

        ProcessTransformerJob::$method($transformerClass, $this->url, $urlContent);
    }

    public function regenerateNow(): void
    {
        $this->regenerate(true);
    }
}
