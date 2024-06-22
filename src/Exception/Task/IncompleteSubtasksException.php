<?php

namespace App\Exception\Task;

use App\Enum\ResponseMessageEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IncompleteSubtasksException extends HttpException
{
    public function __construct(
        string $message = ResponseMessageEnum::CANNOT_COMPLETE_TASK->value,
        int $statusCode = 400,
        \Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}
