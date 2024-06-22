<?php

namespace App\Controller\Api\v1;

use App\Attributes\OpenApi\Adapters as OA;
use App\DTO\DTOFactory;
use App\DTO\TaskDTO;
use App\Entity\Task;
use App\Enum\ResponseMessageEnum;
use App\Enum\TaskKeysEnum;
use App\Helpers\ControllerHelper;
use App\Service\ResponseService;
use App\Service\TaskService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/v1/api/tasks', name: 'task_')]
class TaskController extends AbstractController
{
    use ControllerHelper;

    public function __construct(
        private readonly TaskService $taskService,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseService $responseService
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\GetAdapter(
        'Get list of tasks',
        Task::class,
        [
            '200' => ResponseMessageEnum::TASKS_LIST->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::TASK_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ],
        true
    )]
    #[Security(name: 'Bearer')]
    public function list(Request $request): JsonResponse
    {
        $user = $this->getUserOrThrow();

        $filters = $request->query->all();
        $sorts = $filters['sorts'] ?? [];
        unset($filters['sorts']);

        $tasks = $this->taskService->findAllTasks($user, $filters, $sorts);

        return $this->responseService->jsonResponse($tasks);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\PostAdapter(
        'Create a new task',
        TaskDTO::class,
        Task::class,
        [
            '201' => ResponseMessageEnum::TASK_CREATED_SUCCESSFULLY->value,
            '400' => ResponseMessageEnum::TASK_FIELD_REQUIRED->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::PARENT_TASK_BELONG_ANOTHER_USER->value . ' / ' .
                ResponseMessageEnum::PARENT_TASK_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUserOrThrow();

        $requiredFields = [
            TaskKeysEnum::TITLE->value,
            TaskKeysEnum::DESCRIPTION->value,
            TaskKeysEnum::STATUS->value,
            TaskKeysEnum::PRIORITY->value
        ];
        $taskDTO = $this->dtoFactory->createTaskDTOFromRequest($request, $requiredFields);

        $task = $this->taskService->createTask($user, $taskDTO);

        return $this->responseService->jsonResponse($task, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\PutAdapter(
        'Update a task',
        TaskDTO::class,
        Task::class,
        [
            '200' => ResponseMessageEnum::TASK_UPDATED_SUCCESSFULLY->value,
            '400' => ResponseMessageEnum::TASK_FIELD_REQUIRED->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::TASK_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $user = $this->getUserOrThrow();

        $taskDTO = $this->dtoFactory->createTaskDTOFromRequest($request);
        $task = $this->taskService->editTask($id, $user, $taskDTO);

        return $this->responseService->jsonResponse($task);
    }

    #[Route('/{id}/complete', name: 'complete', methods: ['PATCH'])]
    #[OA\PatchAdapter(
        'Complete a task',
        Task::class,
        [
            '200' => ResponseMessageEnum::TASK_COMPLETED_SUCCESSFULLY->value,
            '400' => ResponseMessageEnum::CANNOT_COMPLETE_TASK->value . ' / ' .
                ResponseMessageEnum::TASK_IS_ALREADY_COMPLETED->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '404' => ResponseMessageEnum::TASK_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function complete(int $id): JsonResponse
    {
        $user = $this->getUserOrThrow();

        $task = $this->taskService->completeTask($id, $user);

        return $this->responseService->jsonResponse($task);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\DeleteAdapter(
        'Delete a task',
        null,
        [
            '200' => ResponseMessageEnum::TASK_DELETED_SUCCESSFULLY->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '400' => ResponseMessageEnum::CANNOT_DELETE_COMPLETED_TASK->value . ' / ' .
                ResponseMessageEnum::TASK_HAS_SUBTASKS->value,
            '404' => ResponseMessageEnum::TASK_NOT_FOUND->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ]
    )]
    #[Security(name: 'Bearer')]
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUserOrThrow();

        $result = $this->taskService->deleteTask($id, $user);

        return $this->responseService->jsonResponse($result);
    }

    #[Route('/tree', name: 'tree', methods: ['GET'])]
    #[OA\GetAdapter(
        'Get task tree',
        Task::class,
        [
            '200' => ResponseMessageEnum::TASK_TREE->value,
            '401' =>
                ResponseMessageEnum::JWT_NOT_FOUND->value . ' / ' .
                ResponseMessageEnum::JWT_EXPIRED->value . ' / ' .
                ResponseMessageEnum::JWT_INVALID->value,
            '500' => ResponseMessageEnum::INTERNAL_SERVER_ERROR->value
        ],
        true
    )]
    #[Security(name: 'Bearer')]
    public function tree(): JsonResponse
    {
        $user = $this->getUserOrThrow();

        $taskTree = $this->taskService->getTaskTree($user);

        return $this->responseService->jsonResponse($taskTree);
    }
}
