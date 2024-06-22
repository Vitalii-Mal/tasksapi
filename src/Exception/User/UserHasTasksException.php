<?php

namespace App\Exception\User;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserHasTasksException extends HttpException
{
    public function __construct(
        $message = ResponseMessageEnum::USER_HAS_TASKS,
        $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct(400, $message, $previous, [], $code);
    }
}
