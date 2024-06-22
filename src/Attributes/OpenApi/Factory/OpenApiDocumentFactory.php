<?php

namespace App\Attributes\OpenApi\Factory;

use App\Service\ResponseService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\RequestBody;

class OpenApiDocumentFactory
{
    public static function createRequestBody(string|object|null $requestModelType): RequestBody
    {
        return new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: $requestModelType))
        );
    }

    public static function createResponses(
        array $responses,
        string|object|null $responseModelType = null,
        bool $isListResponses = false
    ): array {
        return array_map(
            fn ($description, $response) => self::createResponse(
                $response,
                $description,
                $responseModelType,
                $isListResponses
            ),
            $responses,
            array_keys($responses)
        );
    }

    private static function createResponse(
        int $status,
        string|array $description,
        string|object|null $responseModelType,
        bool $isListResponses
    ): OA\Response {
        $contentProperties = [
            new OA\Property(
                property: 'status',
                type: 'string',
                example: in_array($status, [200, 201]) ? 'success' : 'error'
            ),
        ];

        if (in_array($status, [200, 201])) {
            $contentProperties[] = $responseModelType
                ? self::createDataProperty($responseModelType, $isListResponses)
                : new OA\Property(
                    property: 'data',
                    type: 'string',
                    example: $description
                );
        } else {
            $contentProperties[] = new OA\Property(
                property: 'message',
                type: 'string',
                example: $description
            );
        }

        return new OA\Response(
            response: (string)$status,
            description: $description,
            content: new OA\JsonContent(properties: $contentProperties)
        );
    }

    private static function createDataProperty(
        string|object|null $responseModelType,
        bool $isListResponses
    ): OA\Property {
        return $isListResponses
            ? new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(ref: new Model(type: $responseModelType))
            )
            : new OA\Property(
                property: 'data',
                ref: new Model(type: $responseModelType, groups: [ResponseService::GROUP])
            );
    }
}
