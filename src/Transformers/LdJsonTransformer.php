<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Stringable;

class LdJsonTransformer extends Transformer implements HasStructuredOutput
{
    public function instructions(): Stringable|string
    {
        return 'Summarize the following webpage to ld+json. Use the schema.org types and properties that fit the content best, and make the snippet as complete as possible. Put the ld+json in the `json` key.';
    }

    /** @return array<string, Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'json' => $schema->string()->required(),
        ];
    }

    protected function resultFrom(AgentResponse $response): string
    {
        $json = $response instanceof StructuredAgentResponse
            ? $response['json']
            : $response->text;

        // The schema validates the outer response; this validates its flexible JSON payload.
        json_decode($json, flags: JSON_THROW_ON_ERROR);

        return $json;
    }
}
