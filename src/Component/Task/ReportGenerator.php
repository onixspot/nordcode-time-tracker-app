<?php

namespace App\Component\Task;

use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use function dd;

class ReportGenerator implements ReportGeneratorInterface
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

    public function generate(DateTimeInterface $dateStart, DateTimeInterface $dateEnd): ArrayCollection
    {
        $report = new ReportContext();
        $tasks  = $this->repository->getTasks($this->getUser(), $dateStart, $dateEnd);
        dd($tasks);
        // return ;
    }

    private function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }
}