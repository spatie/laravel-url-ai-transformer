<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Support\Str;
use Laravel\Ai\Ai;
use Laravel\Ai\Attributes\Model as ModelAttribute;
use Laravel\Ai\Attributes\Provider as ProviderAttribute;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;
use ReflectionAttribute;
use ReflectionClass;
use Spatie\LaravelUrlAiTransformer\Enums\Model as ModelPreference;
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;
use Spatie\LaravelUrlAiTransformer\Support\Config;
use Stringable;

abstract class Transformer implements Agent
{
    use Promptable;

    public string $url;

    public string $urlContent;

    public ?TransformationResult $transformationResult = null;

    public function instructions(): Stringable|string
    {
        return '';
    }

    public function transform(): void
    {
        $attributes = $this->aiAttributes();

        $response = $this->prompt(
            prompt: $this->content(),
            provider: $this->resolveProvider($attributes),
            model: $this->resolveModel($attributes),
        );

        $this->transformationResult->result = $this->resultFrom($response, $this->transformationResult);
    }

    public function content(): string
    {
        return $this->urlContent;
    }

    /**
     * A #[Provider] attribute hands provider resolution to Laravel AI.
     *
     * @param  list<class-string>  $attributes
     */
    protected function resolveProvider(array $attributes): ?Lab
    {
        if (in_array(ProviderAttribute::class, $attributes, true)) {
            return null;
        }

        return Config::aiProvider();
    }

    /**
     * A #[Provider] or model attribute hands model resolution to Laravel AI.
     *
     * @param  list<class-string>  $attributes
     */
    protected function resolveModel(array $attributes): ?string
    {
        if ($this->overridesModel($attributes)) {
            return null;
        }

        return $this->configuredModel();
    }

    /**
     * @param  list<class-string>  $attributes
     */
    protected function overridesModel(array $attributes): bool
    {
        return array_intersect([
            ProviderAttribute::class,
            ModelAttribute::class,
            UseCheapestModel::class,
            UseSmartestModel::class,
        ], $attributes) !== [];
    }

    protected function configuredModel(): string
    {
        $model = Config::aiModel();

        if ($model instanceof ModelPreference) {
            return $model->resolve(Ai::textProvider(Config::aiProvider()->value));
        }

        return $model;
    }

    /**
     * Return the value stored on the transformation result. Override this to
     * post-process the response, or to set extra data on the given model.
     *
     * A transformer that defines a schema (implements HasStructuredOutput)
     * receives a structured response, which we store as JSON.
     */
    protected function resultFrom(AgentResponse $response, TransformationResult $transformationResult): string
    {
        if ($response instanceof StructuredAgentResponse) {
            return $response->toJson();
        }

        return $response->text;
    }

    /**
     * @return list<class-string>
     */
    protected function aiAttributes(): array
    {
        return array_map(
            fn (ReflectionAttribute $attribute): string => $attribute->getName(),
            (new ReflectionClass($this))->getAttributes(),
        );
    }

    public function setTransformationProperties(
        string $url,
        string $urlContent,
        TransformationResult $transformationResult,
    ): self {
        $this->url = $url;
        $this->urlContent = $urlContent;
        $this->transformationResult = $transformationResult;

        return $this;
    }

    public function type(): string
    {
        return Str::of(static::class)
            ->classBasename()
            ->beforeLast('Transformer')
            ->lcfirst();
    }

    public function shouldRun(): bool
    {
        return true;
    }
}
