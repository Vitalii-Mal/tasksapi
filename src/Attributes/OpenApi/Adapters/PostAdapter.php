<?php

namespace App\Attributes\OpenApi\Adapters;

use App\Attributes\OpenApi\Factory\OpenApiDocumentFactory;
use OpenApi\Attributes\Post;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PostAdapter extends Post
{
    public function __construct(
        string $summary,
        string|object|null $typeModelRequestContent,
        string|object|null $typeModelResponseProperty,
        array $responses
    ) {
        parent::__construct(
            summary: $summary,
            requestBody: OpenApiDocumentFactory::createRequestBody($typeModelRequestContent),
            responses: OpenApiDocumentFactory::createResponses($responses, $typeModelResponseProperty)
        );
    }
}
