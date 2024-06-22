<?php

namespace App\Helpers;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\User;

trait ControllerHelper
{
    private function getUserOrThrow(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException(ResponseMessageEnum::USER_IS_NOT_AUTHORIZED->value);
        }

        return $user;
    }
}
