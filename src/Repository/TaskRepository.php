<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getUserTasksQuery(UserInterface $user): Query
    {
        return $this->createQueryBuilder('t')
            ->addCriteria($this->createUserOwnedCriteria($user))
            ->getQuery();

        // return $qb->('t')
        //     ->where('t.user = :user')
        //     ->setParameter('user', $user)
        //     ->getQuery();
    }

    public function createUserOwnedCriteria(UserInterface $user): Criteria
    {
        return Criteria::create()->andWhere(Criteria::expr()->eq('user', $user));
    }

    public function getTasks(UserInterface $user, DateTimeInterface $dateStart, DateTimeInterface $dateEnd): ArrayCollection
    {
        $query = $this->createQueryBuilder('t')
            ->select('GROUP_CONCAT(DISTINCT t.id) AS tasks')
            ->addSelect('SEC_TO_TIME(SUM(TIME_TO_SEC(t.timeSpent))) AS total_spent_time')
            ->where('t.user = :user')
            ->andWhere('t.date BETWEEN :date_start AND :date_end')
            ->groupBy('t.user')
            ->setParameter('user', $user)
            ->setParameter('date_start', $dateStart)
            ->setParameter('date_end', $dateEnd)
            ->getQuery();

        return new ArrayCollection($query->getResult(AbstractQuery::HYDRATE_OBJECT));
    }
}
