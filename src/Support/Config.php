<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Laravel\Ai\Enums\Lab;
use Spatie\LaravelUrlAiTransformer\Enums\Model as AiModel;
use Spatie\LaravelUrlAiTransformer\Exceptions\InvalidConfig;
use Spatie\LaravelUrlAiTransformer\Jobs\ProcessTransformerJob;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

class Config
{
    /**
     * @template T of object
     *
     * @param  class-string<T>  $mustBeOrExtend
     * @return class-string<T>
     */
    public static function getActionClass(string $actionKey, string $mustBeOrExtend): string
    {
        $actionClass = config("url-ai-transformer.actions.{$actionKey}");

        if (! $actionClass) {
            throw InvalidConfig::actionKeyNotFound($actionKey);
        }

        if (! class_exists($actionClass)) {
            throw InvalidConfig::actionClassDoesNotExist($actionClass);
        }

        if (! is_a($actionClass, $mustBeOrExtend, true)) {
            throw InvalidConfig::actionClassDoesNotExtend($actionClass, $mustBeOrExtend);
        }

        return $actionClass;
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $mustBeOrExtend
     * @return T
     */
    public static function getAction(string $actionKey, string $mustBeOrExtend): object
    {
        $actionClass = self::getActionClass($actionKey, $mustBeOrExtend);

        return app($actionClass);
    }

    /**
     * @return class-string<TransformationResult>
     */
    public static function model(): string
    {
        $modelClass = config('url-ai-transformer.model');

        if (! $modelClass) {
            throw InvalidConfig::modelClassNotConfigured();
        }

        if (! class_exists($modelClass)) {
            throw InvalidConfig::modelClassDoesNotExist($modelClass);
        }

        if (! is_a($modelClass, TransformationResult::class, true)) {
            throw InvalidConfig::modelClassDoesNotExtend($modelClass, TransformationResult::class);
        }

        return $modelClass;
    }

    public static function aiProvider(): Lab
    {
        $provider = config('url-ai-transformer.ai.provider');

        if (! $provider) {
            throw InvalidConfig::aiProviderNotConfigured();
        }

        if (! $provider instanceof Lab) {
            throw InvalidConfig::invalidAiProvider();
        }

        return $provider;
    }

    public static function aiModel(): string|AiModel
    {
        $model = config('url-ai-transformer.ai.model');

        if (! $model) {
            throw InvalidConfig::aiModelNotConfigured();
        }

        if (! is_string($model) && ! $model instanceof AiModel) {
            throw InvalidConfig::invalidAiModel();
        }

        return $model;
    }

    /**
     * @return class-string<ProcessTransformerJob>
     */
    public static function getProcessTransformationJobClass(): string
    {
        $jobClass = config('url-ai-transformer.process_transformer_job');

        if (! $jobClass) {
            throw InvalidConfig::jobClassNotConfigured();
        }

        if (! class_exists($jobClass)) {
            throw InvalidConfig::jobClassDoesNotExist($jobClass);
        }

        if (! is_a($jobClass, ProcessTransformerJob::class, true)) {
            throw InvalidConfig::jobClassDoesNotExtend($jobClass, ProcessTransformerJob::class);
        }

        return $jobClass;
    }
}
