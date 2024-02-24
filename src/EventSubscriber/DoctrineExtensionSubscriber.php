<?php

namespace App\EventSubscriber;

use Gedmo\Blameable\BlameableListener;
use Gedmo\Loggable\LoggableListener;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class DoctrineExtensionSubscriber implements EventSubscriberInterface
{
    private BlameableListener $blameableListener;

    private TokenStorageInterface $tokenStorage;

    private TranslatableListener $translatableListener;

    private LoggableListener $loggableListener;

    public function __construct(
        BlameableListener $blameableListener,
        TokenStorageInterface $tokenStorage,
        TranslatableListener $translatableListener,
        LoggableListener $loggableListener
    ) {
        $this->blameableListener = $blameableListener;
        $this->tokenStorage = $tokenStorage;
        $this->translatableListener = $translatableListener;
        $this->loggableListener = $loggableListener;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::FINISH_REQUEST => 'onLateKernelRequest',
        ];
    }

    public function onKernelRequest(): void
    {
        if (
            null !== $this->tokenStorage->getToken()
            && null !== $this->tokenStorage->getToken()->getUser()
        ) {
            $this->blameableListener->setUserValue($this->tokenStorage->getToken()->getUser());
        }
    }

    public function onLateKernelRequest(FinishRequestEvent $event): void
    {
        $this->translatableListener->setTranslatableLocale($event->getRequest()->getLocale());
    }
}
