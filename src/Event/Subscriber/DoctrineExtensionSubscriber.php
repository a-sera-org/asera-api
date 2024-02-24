<?php

namespace App\Event\Subscriber;

use Gedmo\Blameable\BlameableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class DoctrineExtensionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BlameableListener $blameableListener, private TokenStorageInterface $tokenStorage)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(): void
    {
        if (null !== $this->tokenStorage->getToken() && null !== $this->tokenStorage->getToken()->getUser()) {
            $this->blameableListener->setUserValue($this->tokenStorage->getToken()->getUser());
        }
    }
}
