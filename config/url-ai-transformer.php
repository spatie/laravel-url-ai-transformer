<?php

return [
    'ai' => [
        'default' => [
            'provider' => Prism\Prism\Enums\Provider::OpenAI,
            'model' => 'gpt-4o-mini',
        ],

        'image' => [
            'provider' => Prism\Prism\Enums\Provider::OpenAI,
            'model' => 'dall-e-3',
        ],
    ],

    'model' => \Spatie\LaravelUrlAiTransformer\Models\TransformationResult::class,

    'actions' => [
        'process_registration' => Spatie\LaravelUrlAiTransformer\Actions\ProcessRegistrationAction::class,
    ],
];
