<?php

namespace App\Exception\User;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserNotFoundException extends HttpException
{
    public function __construct(
        string $message = ResponseMessageEnum::USER_NOT_FOUND->value,
        int $statusCode = 404,
        \Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}
