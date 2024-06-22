<?php

namespace App\Exception\User;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAlreadyExistsException extends HttpException
{
    public function __construct($message = ResponseMessageEnum::USER_ALREADY_EXIST->value)
    {
        parent::__construct(400, $message);
    }
}
