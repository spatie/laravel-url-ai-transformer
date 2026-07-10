<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Support\Str;
use Laravel\Ai\Attributes\Model as ModelAttribute;
use Laravel\Ai\Attributes\Provider as ProviderAttribute;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use ReflectionClass;
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
        $overridesProvider = $this->hasAiAttribute(ProviderAttribute::class);

        $overridesModel = $overridesProvider
            || $this->hasAiAttribute(ModelAttribute::class)
            || $this->hasAiAttribute(UseCheapestModel::class)
            || $this->hasAiAttribute(UseSmartestModel::class);

        $response = $this->prompt(
            prompt: $this->content(),
            provider: $overridesProvider ? null : Config::aiProvider(),
            model: $overridesModel ? null : Config::aiModel(),
        );

        $this->transformationResult->result = $response->text;
    }

    public function content(): string
    {
        return $this->urlContent;
    }

    protected function hasAiAttribute(string $attribute): bool
    {
        return (new ReflectionClass($this))->getAttributes($attribute) !== [];
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
