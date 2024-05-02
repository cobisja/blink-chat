<?php

namespace App\EventSubscriber\Auth;

use App\Event\Auth\UserPasswordUpdatedEvent;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class UserPasswordUpdatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserPasswordUpdatedEvent::class => 'onUserPasswordUpdatedEvent',
        ];
    }

    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws TransportExceptionInterface
     */
    public function onUserPasswordUpdatedEvent(UserPasswordUpdatedEvent $event): void
    {
        $email = $event->payload()['email'];

        if (!$user = $this->userRepository->findByEmail($email)) {
            throw new UserNotFoundException();
        }

        $email = (new TemplatedEmail())
            ->to(new Address($email))
            ->subject('Your Blink-Chat Account Password Has Been Changed')
            ->htmlTemplate('emails/auth/passwords/user_password_updated.html.twig')
            ->context([
                'username' => $user->getFullName()
            ]);

        $this->mailer->send($email);
    }
}
