<?php

// config for Spatie/LaravelUrlAiTransformer
return [
    'ai' => [
        'default' => [
            'provider' => \Prism\Prism\Enums\Provider::Anthropic,
            'model' => 'claude-3-5-sonnet-20240620',
        ],

        'image' => [
            'provider' => \Prism\Prism\Enums\Provider::Anthropic,
            'model' => 'claude-3-5-sonnet-20240620',
        ],
    ],

    'model' => \Spatie\LaravelUrlAiTransformer\Models\TransformationResult::class,

    'actions' => [
        'process_registration' => Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction::class,
    ],
];
