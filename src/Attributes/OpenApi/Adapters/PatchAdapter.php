<?php

namespace App\Attributes\OpenApi\Adapters;

use App\Attributes\OpenApi\Factory\OpenApiDocumentFactory;
use OpenApi\Attributes\Patch;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PatchAdapter extends Patch
{
    public function __construct(
        string $summary,
        string|object|null $typeModelResponseProperty,
        array $responses
    ) {
        parent::__construct(
            summary: $summary,
            responses: OpenApiDocumentFactory::createResponses($responses, $typeModelResponseProperty)
        );
    }
}
