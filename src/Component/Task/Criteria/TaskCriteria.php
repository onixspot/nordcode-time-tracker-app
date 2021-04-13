<?php

namespace App\Component\Task\Criteria;

use DateTimeInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskCriteria
 *
 * @method static ExpressionBuilder expr
 * @method static TaskCriteria|Criteria create
 */
class TaskCriteria extends Criteria
{
    public function withUser(UserInterface $user): TaskCriteria
    {
        return $this->where(self::expr()->eq('user', $user));
    }
}