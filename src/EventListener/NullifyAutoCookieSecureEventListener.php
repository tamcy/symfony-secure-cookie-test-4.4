<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class NullifyAutoCookieSecureEventListener implements EventSubscriberInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->session->set('baz', '123');
    }

    public static function getSubscribedEvents()
    {
        return [
            // After the patch, SessionListener will still not run if there is an
            // event listener that accesses the session registered with priority >= 128.
            KernelEvents::REQUEST => ['onKernelRequest', 128],
        ];
    }
}
