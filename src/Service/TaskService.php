<?php

namespace App\Service;

use App\Repository\TaskRepository;
use DateTimeInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TaskService
{
    private TaskRepository $repository;

    private Security $security;

    private SerializerInterface $serializer;

    public function __construct(TaskRepository $repository, Security $security, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->security   = $security;
        $this->serializer = $serializer;
    }

    public function generate(DateTimeInterface $dateStart, DateTimeInterface $dateEnd)
    {
        return $this->repository->getTasks($this->getUser(), $dateStart, $dateEnd);
    }

    private function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }
}