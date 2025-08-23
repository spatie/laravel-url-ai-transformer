<?php

namespace Spatie\LaravelUrlAiTransformer\Support;

use Illuminate\Support\Collection;
use Spatie\LaravelUrlAiTransformer\Exceptions\CouldNotFindTransformer;

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

    public function getTransformationClassForType(string $type): string
    {
        foreach ($this->registrations as $registration) {
            foreach ($registration->getTransformers() as $transformer) {
                if ($transformer->type() === $type) {
                    return get_class($transformer);
                }
            }
        }

        throw CouldNotFindTransformer::make($type);
    }
}
