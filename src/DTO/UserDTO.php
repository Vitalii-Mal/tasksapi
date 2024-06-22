<?php

namespace App\DTO;

use App\Enum\UserKeysEnum;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserDTO extends AbstractDTO
{
    #[SerializedName(UserKeysEnum::USERNAME->value)]
    private string $username;

    #[SerializedName(UserKeysEnum::PASSWORD->value)]
    private string $password;

    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);
    }

    protected function setPropertiesFromRequestData(array $requestData): self
    {
        $this->setUsername($requestData[UserKeysEnum::USERNAME->value]);
        $this->setPassword($requestData[UserKeysEnum::PASSWORD->value]);

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
