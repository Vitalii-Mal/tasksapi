<?php

namespace App\DTO;

use App\Enum\ResponseMessageEnum;
use App\Enum\UserKeysEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class DTOFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function createUserDTOFromRequest(Request $request): UserDTO
    {
        $requestData = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException(ResponseMessageEnum::INVALID_JSON->value);
        }

        $requiredFields = [UserKeysEnum::USERNAME->value, UserKeysEnum::PASSWORD->value];
        $userDTO = new UserDTO($this->validator);
        $userDTO->createDTO($requestData, $requiredFields);

        return $userDTO;
    }

    public function createTaskDTOFromRequest(Request $request, array $requiredFields = []): TaskDTO
    {
        $requestData = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException(ResponseMessageEnum::INVALID_JSON->value);
        }

        $taskDTO = new TaskDTO($this->validator);
        $taskDTO->createDTO($requestData, $requiredFields);

        return $taskDTO;
    }
}
