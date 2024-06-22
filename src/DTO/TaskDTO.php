<?php

namespace App\DTO;

use App\Enum\TaskKeysEnum;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskDTO extends AbstractDTO
{
    #[SerializedName(TaskKeysEnum::TITLE->value)]
    private ?string $title;

    #[SerializedName(TaskKeysEnum::DESCRIPTION->value)]
    private ?string $description;

    #[SerializedName(TaskKeysEnum::STATUS->value)]
    private ?string $status;

    #[SerializedName(TaskKeysEnum::PRIORITY->value)]
    private ?int $priority;

    #[SerializedName(TaskKeysEnum::PARENT_TASK_ID->value)]
    private ?int $parentTaskId;

    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);
    }

    protected function setPropertiesFromRequestData(array $requestData): self
    {
        $this->setTitle($requestData[TaskKeysEnum::TITLE->value] ?? null);
        $this->setDescription($requestData[TaskKeysEnum::DESCRIPTION->value] ?? null);
        $this->setStatus($requestData[TaskKeysEnum::STATUS->value] ?? null);
        $this->setPriority($requestData[TaskKeysEnum::PRIORITY->value] ?? null);
        $this->setParentTaskId($requestData[TaskKeysEnum::PARENT_TASK_ID->value] ?? null);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getParentTaskId(): ?int
    {
        return $this->parentTaskId;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    public function setParentTaskId(?int $parentTaskId): void
    {
        $this->parentTaskId = $parentTaskId;
    }
}
