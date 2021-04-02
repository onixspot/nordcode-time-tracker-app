<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RequestSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST    => ['handleRequest'],
            KernelEvents::RESPONSE   => ['handleResponse'],
            KernelEvents::EXCEPTION  => ['handleException'],
            KernelEvents::CONTROLLER => ['onKernelController'],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
    }

    public function handleException(ExceptionEvent $event): void
    {
    }

    public function handleResponse(ResponseEvent $event): void
    {
    }

    public function handleRequest(RequestEvent $event): void
    {
    }
}
