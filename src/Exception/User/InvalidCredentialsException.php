<?php

namespace App\Exception\User;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCredentialsException extends HttpException
{
    public function __construct(
        string $message = ResponseMessageEnum::INVALID_CREDENTIALS->value,
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
