<?php

namespace App\Service;

use App\DTO\TaskDTO;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\ResponseMessageEnum;
use App\Enum\StatusEnum;
use App\Enum\TaskKeysEnum;
use App\Enum\UserKeysEnum;
use App\Exception\Task\IncompleteSubtasksException;
use App\Exception\Task\TaskAlreadyCompletedException;
use App\Exception\Task\TaskHasSubtasksException;
use App\Exception\Task\TaskNotFoundException;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function findAllTasks(User $user, array $filters, array $sorts): array
    {
        $filters[UserKeysEnum::USER->value] = $user->getId();

        $tasks = $this->taskRepository->findTasks($filters, $sorts);

        if (empty($tasks)) {
            throw new TaskNotFoundException();
        }

        return $tasks;
    }

    public function createTask(User $user, TaskDTO $taskDTO): Task
    {
        $task = new Task();
        $task->setTitle($taskDTO->getTitle());
        $task->setDescription($taskDTO->getDescription());
        $task->setStatus($taskDTO->getStatus());
        $task->setPriority($taskDTO->getPriority());
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUser($user);

        $parentTaskId = $taskDTO->getParentTaskId();

        if(!is_null($parentTaskId)) {
            $parentTask = $this->taskRepository->find($taskDTO->getParentTaskId());
            if (!$parentTask) {
                throw new TaskNotFoundException(
                    sprintf(
                        ResponseMessageEnum::PARENT_TASK_NOT_FOUND->value . 'id %d',
                        $parentTaskId
                    )
                );
            }

            $userId = $user->getId();
            $parentTaskUserId = $parentTask->getUser()->getId();

            if ($parentTaskUserId !== $userId) {
                throw new TaskNotFoundException(
                    ResponseMessageEnum::PARENT_TASK_BELONG_ANOTHER_USER->value
                );
            }

            $task->setParentTask($parentTask);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function editTask(int $id, User $user, TaskDTO $taskDTO): Task
    {
        $task = $this->taskRepository->findOneBy([
            TaskKeysEnum::ID->value => $id,
            UserKeysEnum::USER->value => $user->getId()
        ]);
        if (!$task) {
            throw new TaskNotFoundException();
        }

        $task->setTitle($taskDTO->getTitle() ?? $task->getTitle());
        $task->setDescription($taskDTO->getDescription() ?? $task->getDescription());
        $task->setStatus($taskDTO->getStatus() ?? $task->getStatus());
        $task->setPriority($taskDTO->getPriority() ?? $task->getPriority());

        if (!is_null($taskDTO->getParentTaskId())) {
            $parentTask = $this->taskRepository->find($taskDTO->getParentTaskId());
            $task->setParentTask($parentTask);
        }

        $this->entityManager->flush();

        return $task;
    }

    public function completeTask(int $id, User $user): Task
    {
        $task = $this->taskRepository->findOneBy([
            TaskKeysEnum::ID->value => $id,
            UserKeysEnum::USER->value => $user->getId()
        ]);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        if ($task->getStatus() === StatusEnum::DONE->value) {
            throw new TaskAlreadyCompletedException();
        }

        foreach ($task->getSubtasks() as $subtask) {
            if ($subtask->getStatus() !== StatusEnum::DONE->value) {
                throw new IncompleteSubtasksException(
                    ResponseMessageEnum::CANNOT_COMPLETE_TASK->value .
                    ' id ' .
                    $subtask->getId()
                );
            }
        }

        $task->setStatus(StatusEnum::DONE->value);
        $task->setCompletedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $task;
    }

    public function deleteTask(int $id, User $user): string
    {
        $task = $this->taskRepository->findOneBy([
            TaskKeysEnum::ID->value => $id,
            UserKeysEnum::USER->value => $user->getId()
        ]);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        if ($task->getStatus() === StatusEnum::DONE->value) {
            throw new TaskHasSubtasksException(
                ResponseMessageEnum::CANNOT_DELETE_COMPLETED_TASK->value
            );
        }

        if (count($task->getSubtasks()) > 0) {
            throw new TaskHasSubtasksException();
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return ResponseMessageEnum::TASK_DELETED_SUCCESSFULLY->value;
    }

    public function getTaskTree(User $user): array
    {
        $userId = $user->getId();
        $tasks = $this->taskRepository->findBy(
            [UserKeysEnum::USER->value => $userId, TaskKeysEnum::PARENT_TASK->value => null]
        );
        return array_map(fn (Task $task) => $this->buildTaskTree($task), $tasks);
    }

    private function buildTaskTree(Task $task): array
    {
        $subtasks = array_map(
            fn (Task $subtask) => $this->buildTaskTree($subtask),
            $task->getSubtasks()->toArray()
        );

        return [
            TaskKeysEnum::ID->value => $task->getId(),
            TaskKeysEnum::TITLE->value => $task->getTitle(),
            TaskKeysEnum::DESCRIPTION->value => $task->getDescription(),
            TaskKeysEnum::STATUS->value => $task->getStatus(),
            TaskKeysEnum::PRIORITY->value => $task->getPriority(),
            TaskKeysEnum::CREATED_AT->value => $task->getCreatedAt(),
            TaskKeysEnum::COMPLETED_AT->value => $task->getCompletedAt(),
            TaskKeysEnum::SUBTASKS->value => $subtasks,
        ];
    }
}
