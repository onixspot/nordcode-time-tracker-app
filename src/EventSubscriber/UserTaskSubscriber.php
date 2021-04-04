<?php

namespace App\EventSubscriber;

use App\Controller\TaskController;
use App\Entity\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class UserTaskSubscriber implements EventSubscriberInterface
{
    const ROUTES = [
        'show',
        'edit',
        'delete',
    ];
    private Security $security;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security     = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'handleControllerArguments',
            KernelEvents::EXCEPTION            => 'handleException',
        ];
    }

    public function handleException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof AccessDeniedHttpException) {
            $response = new RedirectResponse($this->urlGenerator->generate('task_index'));
            $event->setResponse($response);
            $event->allowCustomResponseCode();
        }
    }

    public function handleControllerArguments(ControllerArgumentsEvent $event): void
    {
        $controller = $event->getController();
        $user       = $this->security->getUser();

        if (
            !is_array($controller)
            || !$controller[0] instanceof TaskController
            || !in_array($controller[1], self::ROUTES, true)
            || $this->resolveTask($event->getArguments())->getUser() === $user
        ) {
            return;
        }

        throw new AccessDeniedException();
    }

    private function resolveTask(array $args): Task
    {
        return $args[0];
    }
}
