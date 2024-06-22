<?php

namespace App\Exception\Task;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskAlreadyCompletedException extends HttpException
{
    public function __construct(
        string $message = ResponseMessageEnum::TASK_IS_ALREADY_COMPLETED->value,
        int $statusCode = 400,
        \Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}
