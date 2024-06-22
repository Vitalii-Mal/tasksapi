<?php

namespace App\Service;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

readonly class ResponseService
{
    public const GROUP = 'task';

    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function jsonResponse(
        mixed $data,
        int $status = SymfonyResponse::HTTP_OK
    ): JsonResponse {
        $data = $this->successResponse($data);
        $data = $this->serialize($data);

        return new JsonResponse($data, $status, [], true);
    }

    private function serialize(mixed $data, string $group = self::GROUP): string
    {
        return $this->serializer->serialize($data, 'json', [
            'groups' => $group,
            'json_encode_options' => JSON_UNESCAPED_UNICODE
        ]);
    }

    private function successResponse($data = null): array
    {
        return ['status' => 'success', 'data' => $data];
    }

    public function errorResponse(
        string $message = ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
    ): array {
        return ['status' => 'error', 'message' => $message];
    }
}
