<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Illuminate\Support\Collection;

class RegisteredTransformations
{
    protected array $registrations = [];

    public function add(TransformationRegistration $registration): void
    {
        $this->registrations[] = $registration;
    }

    /**
     * @return Collection<int, TransformationRegistration>
     */
    public function all(): Collection
    {
        return collect($this->registrations);
    }

    public function clear(): void
    {
        $this->registrations = [];
    }
}
