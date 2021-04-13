<?php

namespace App\Component\Task\Generator;

use App\Component\Task\ReportGeneratorInterface;
use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\ORM\Query\QueryException;

class ReportGenerator implements ReportGeneratorInterface
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @throws QueryException
     */
    public function generate(DateTimeInterface $dateStart, DateTimeInterface $dateEnd)
    {
        $qb = $this->taskRepository->createQueryBuilder();

        return $qb
            ->andWhere($qb->expr()->between('t.date', ':date_start', ':date_end'))
            ->setParameter('date_start', $dateStart)
            ->setParameter('date_end', $dateEnd)
            ->getQuery()
            ->getArrayResult();
    }
}