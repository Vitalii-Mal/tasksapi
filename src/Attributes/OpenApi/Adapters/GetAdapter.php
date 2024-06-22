<?php

namespace App\Attributes\OpenApi\Adapters;

use App\Attributes\OpenApi\Factory\OpenApiDocumentFactory;
use OpenApi\Attributes\Get;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class GetAdapter extends Get
{
    public function __construct(
        string $summary,
        string|object|null $typeModelResponseProperty,
        array $responses,
        bool $isListResponses = false
    ) {
        parent::__construct(
            summary: $summary,
            responses: OpenApiDocumentFactory::createResponses(
                $responses,
                $typeModelResponseProperty,
                $isListResponses
            )
        );
    }
}
