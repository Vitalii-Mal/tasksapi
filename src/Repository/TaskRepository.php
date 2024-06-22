<?php

namespace App\Repository;

use App\Entity\Task;
use App\Enum\TaskKeysEnum;
use App\Enum\UserKeysEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Task::class);
    }

    public function findTasks(array $filters, array $sorts): array
    {
        $qb = $this->createQueryBuilder('t');

        // Apply filters
        $this->applyFilters($qb, $filters);

        // Apply sorting
        $this->applySorting($qb, $sorts);

        return $qb->getQuery()->getResult();
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        if (!empty($filters[TaskKeysEnum::STATUS->value])) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $filters[TaskKeysEnum::STATUS->value]);
        }

        if (!empty($filters[TaskKeysEnum::PRIORITY->value])) {
            $qb->andWhere('t.priority = :priority')
                ->setParameter('priority', $filters[TaskKeysEnum::PRIORITY->value]);
        }

        if (!empty($filters[TaskKeysEnum::TITLE->value])) {
            $qb->andWhere('t.title LIKE :title')
                ->setParameter('title', '%' . $filters[TaskKeysEnum::TITLE->value] . '%');
        }

        if (!empty($filters[TaskKeysEnum::DESCRIPTION->value])) {
            $qb->andWhere('t.description LIKE :description')
                ->setParameter('description', '%' . $filters[TaskKeysEnum::DESCRIPTION->value] . '%');
        }

        if (!empty($filters[UserKeysEnum::USER->value])) {
            $qb->andWhere('t.user = :user')
                ->setParameter('user', $filters[UserKeysEnum::USER->value]);
        }
    }

    private function applySorting(QueryBuilder $qb, array $sorts): void
    {
        $allowedSortFields = [
            TaskKeysEnum::CREATED_AT->value,
            TaskKeysEnum::COMPLETED_AT->value,
            TaskKeysEnum::PRIORITY->value
        ];

        foreach ($sorts as $sort => $order) {
            if (in_array($sort, $allowedSortFields)) {
                $qb->addOrderBy('t.' . $sort, $order);
            }
        }
    }
}
