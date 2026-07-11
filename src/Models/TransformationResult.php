<?php

namespace Spatie\LaravelUrlAiTransformer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\LaravelUrlAiTransformer\Actions\FetchUrlContentAction;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Spatie\LaravelUrlAiTransformer\Support\RegisteredTransformations;
use Spatie\LaravelUrlAiTransformer\Transformers\Transformer;
use Throwable;

/**
 * @property string $url
 * @property string $type
 * @property ?string $result
 * @property ?Carbon $successfully_completed_at
 * @property ?Carbon $latest_exception_seen_at
 * @property ?string $latest_exception_message
 * @property ?string $latest_exception_trace
 */
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
    ): static {
        return static::query()->firstOrCreate([
            'url' => $url,
            'type' => $transformer->type(),
        ]);
    }

    /**
     * @param  string|class-string<Transformer>  $type
     */
    public static function forUrl(string $url, string $type): ?string
    {
        return static::findForUrl($url, $type)?->result;
    }

    /**
     * @param  string|class-string<Transformer>  $type
     */
    public static function findForUrl(string $url, string $type): ?static
    {
        return static::query()
            ->where('url', $url)
            ->where('type', static::normalizeType($type))
            ->first();
    }

    /**
     * @param  string|class-string<Transformer>  $type
     */
    protected static function normalizeType(string $type): string
    {
        if (is_a($type, Transformer::class, true)) {
            return app($type)->type();
        }

        return $type;
    }

    public function recordException(Throwable $exception): void
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

    public function regenerate(): void
    {
        $this->dispatchRegeneration(now: false);
    }

    public function regenerateNow(): void
    {
        $this->dispatchRegeneration(now: true);
    }

    protected function dispatchRegeneration(bool $now): void
    {
        $transformerClass = app(RegisteredTransformations::class)->getTransformationClassForType($this->type);

        $fetchAction = Config::getAction('fetch_url_content', FetchUrlContentAction::class);

        $urlContent = $fetchAction->execute($this->url);

        $jobClass = Config::getProcessTransformationJobClass();

        $method = $now
            ? 'dispatchSync'
            : 'dispatch';

        $jobClass::$method($transformerClass, $this->url, $urlContent);
    }
}
