<?php

namespace Spatie\LaravelUrlAiTransformer\Enums;

use Laravel\Ai\Contracts\Providers\TextProvider;

enum Model
{
    case Cheapest;
    case Smartest;

    public function resolve(TextProvider $provider): string
    {
        return match ($this) {
            self::Cheapest => $provider->cheapestTextModel(),
            self::Smartest => $provider->smartestTextModel(),
        };
    }
}
