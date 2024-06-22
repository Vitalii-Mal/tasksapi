<?php

namespace App\DTO;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDTO
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    private function validateRequestData(array $requestData, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $requestData)) {
                throw new BadRequestHttpException(sprintf("Field '%s' is required", $field));
            }
        }
    }

    private function validateDTO(self $taskDTO): void
    {
        $errors = $this->validator->validate($taskDTO);
        if (count($errors) > 0) {
            $errorMessages = array_map(fn ($error) => $error->getMessage(), (array) $errors);
            throw new BadRequestHttpException(json_encode(['errors' => $errorMessages]));
        }
    }

    public function createDTO(array $requestData, array $requiredFields): void
    {
        $this->validateRequestData($requestData, $requiredFields);
        $dto = $this->setPropertiesFromRequestData($requestData);
        $this->validateDTO($dto);
    }

    abstract protected function setPropertiesFromRequestData(array $requestData): self;
}
