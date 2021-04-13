<?php

namespace App\Repository;

use App\Component\Task\Criteria\TaskCriteria;
use App\Entity\Task;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\MysqlWalker;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    private TaskCriteria $criteria;

    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Task::class);
        $this->security = $security;
        $this->criteria = TaskCriteria::create()->withUser($this->getUser());
    }

    private function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * @throws QueryException
     */
    public function getTasks(
        UserInterface $user,
        DateTimeInterface $dateStart,
        DateTimeInterface $dateEnd
    ): ArrayCollection {
        $query = $this->createQueryBuilder()
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

    /**
     * @throws QueryException
     */
    public function createQueryBuilder($alias = 't', $indexBy = null): QueryBuilder
    {
        return parent::createQueryBuilder($alias, $indexBy)
            ->addCriteria($this->getCriteria());
    }

    /**
     * @return TaskCriteria|Criteria
     */
    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    /**
     * @throws QueryException
     */
    public function findInDateRange(DateTimeInterface $dateStart, DateTimeInterface $dateEnd)
    {
        $builder = $this->createQueryBuilder();
        $query   = $builder
            ->select('t.id')
            ->addSelect('SUM(t.timeSpent) as timeSpent')
            ->addSelect('ANY_VALUE(t.title) as title')
            ->addSelect('ANY_VALUE(t.comment) as comment')
            ->addSelect('ANY_VALUE(t.date) as date')
            ->andWhere($builder->expr()->between('t.date', ':date_start', ':date_end'))
            ->groupBy('t.id')
            ->setParameter('date_start', $dateStart)
            ->setParameter('date_end', $dateEnd)
            ->getQuery();

        $query
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, MysqlWalker::class)
            ->setHint('mysqlWalker.withRollup', true);

        return $query->getArrayResult();
    }
}
