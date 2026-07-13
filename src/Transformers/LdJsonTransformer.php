<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Responses\AgentResponse;
use Stringable;

class LdJsonTransformer extends Transformer implements HasStructuredOutput
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the following webpage to ld+json. Use the schema.org types and properties that fit the content best, and make the snippet as complete as possible. Put the ld+json in the `json` key.';
    }

    /**
     * The schema guarantees the response is valid JSON, while the ld+json
     * inside the `json` key can take any shape that fits the content.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'json' => $schema->string()->required(),
        ];
    }

    protected function resultFrom(AgentResponse $response): string
    {
        return $response['json'];
    }
}
