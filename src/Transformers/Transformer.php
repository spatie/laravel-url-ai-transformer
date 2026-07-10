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

        // A #[Provider] attribute hands full resolution to Laravel AI. Otherwise we
        // use the configured provider, and a model attribute (#[Model],
        // #[UseCheapestModel], ...) overrides the configured model.
        if (in_array(ProviderAttribute::class, $attributes, true)) {
            $provider = $model = null;
        } else {
            $provider = Config::aiProvider();

            $overridesModel = array_intersect(
                [ModelAttribute::class, UseCheapestModel::class, UseSmartestModel::class],
                $attributes,
            ) !== [];

            $model = $overridesModel ? null : $this->configuredModel($provider);
        }

        $response = $this->prompt(
            prompt: $this->content(),
            provider: $provider,
            model: $model,
        );

        $this->transformationResult->result = $response->text;
    }

    public function content(): string
    {
        return $this->urlContent;
    }

    protected function configuredModel(Lab $provider): string
    {
        $model = Config::aiModel();

        if ($model instanceof ModelPreference) {
            return $model->resolve(Ai::textProvider($provider->value));
        }

        return $model;
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
