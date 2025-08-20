<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

class RegisteredTransformations
{
    protected array $registrations = [];

    public function add(TransformationRegistration $registration): void
    {
        $this->registrations[] = $registration;
    }

    public function all(): array
    {
        return $this->registrations;
    }

    public function clear(): void
    {
        $this->registrations = [];
    }
}
