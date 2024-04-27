<?php

namespace App\EventSubscriber\Auth;

use App\Event\Auth\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegisteredEvent',
        ];
    }

    public function onUserRegisteredEvent(UserRegisteredEvent $event): void
    {
        // TODO: Implement email sending
    }
}
