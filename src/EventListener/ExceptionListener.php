<?php

namespace App\EventListener;

use App\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

readonly class ExceptionListener
{
    public function __construct(private ResponseService $responseService)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse();

        if ($exception instanceof HttpExceptionInterface) {
            $errorResponse = $this->responseService->errorResponse($exception->getMessage());
            $response->setData($errorResponse);
            $response->setStatusCode($exception->getStatusCode());
        } else {
            $errorResponse = $this->responseService->errorResponse();
            $response->setData($errorResponse);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
