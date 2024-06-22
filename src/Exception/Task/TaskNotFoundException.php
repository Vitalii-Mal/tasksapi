<?php

namespace App\Exception\Task;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskNotFoundException extends HttpException
{
    public function __construct(
        string $message = ResponseMessageEnum::TASK_NOT_FOUND->value,
        int $statusCode = 404,
        \Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}
