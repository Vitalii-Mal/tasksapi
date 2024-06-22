<?php

namespace App\Entity;

use App\Enum\TaskKeysEnum;
use App\Service\ResponseService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: 'App\Repository\TaskRepository')]
#[ORM\Index(name: 'title_idx', columns: [TaskKeysEnum::TITLE->value])]
#[ORM\Index(name: 'status_idx', columns: [TaskKeysEnum::STATUS->value])]
#[ORM\Index(name: 'priority_idx', columns: [TaskKeysEnum::PRIORITY->value])]
#[ORM\Index(name: 'created_at_idx', columns: [TaskKeysEnum::CREATED_AT_INDEX->value])]
#[ORM\Index(name: 'user_id_idx', columns: [TaskKeysEnum::USER_ID_INDEX->value])]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: TaskKeysEnum::ID->value, type: "integer")]
    private int $id;

    #[ORM\Column(name: TaskKeysEnum::TITLE->value, type: "string", length: 255)]
    #[Groups([ResponseService::GROUP])]
    private string $title;

    #[ORM\Column(name: TaskKeysEnum::DESCRIPTION->value, type: "text", nullable: true)]
    #[Groups([ResponseService::GROUP])]
    private ?string $description;

    #[ORM\Column(name: TaskKeysEnum::STATUS->value, type: "string", length: 20)]
    #[Groups([ResponseService::GROUP])]
    private string $status;

    #[ORM\Column(name: TaskKeysEnum::PRIORITY->value, type: "integer")]
    #[Groups([ResponseService::GROUP])]
    private int $priority;

    #[ORM\Column(name: TaskKeysEnum::CREATED_AT_INDEX->value, type: "datetime")]
    #[Groups([ResponseService::GROUP])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups([ResponseService::GROUP])]
    private ?\DateTimeInterface $completedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Task $parentTask;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: "parentTask")]
    private Collection $subtasks;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->subtasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getParentTask(): ?self
    {
        return $this->parentTask;
    }

    public function setParentTask(?self $parentTask): self
    {
        $this->parentTask = $parentTask;
        return $this;
    }

    public function getSubtasks(): Collection
    {
        return $this->subtasks;
    }

    public function addSubtask(Task $subtask): self
    {
        if (!$this->subtasks->contains($subtask)) {
            $this->subtasks[] = $subtask;
            $subtask->setParentTask($this);
        }
        return $this;
    }

    public function removeSubtask(Task $subtask): self
    {
        if ($this->subtasks->removeElement($subtask)) {
            // set the owning side to null (unless already changed)
            if ($subtask->getParentTask() === $this) {
                $subtask->setParentTask(null);
            }
        }
        return $this;
    }
}
