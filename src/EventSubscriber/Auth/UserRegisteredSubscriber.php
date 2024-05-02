<?php

namespace App\EventSubscriber\Auth;

use App\Event\Auth\UserRegisteredEvent;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegisteredEvent',
        ];
    }

    public function __construct(private MailerInterface $mailer, private UserRepository $userRepository)
    {
    }

    /**
     * @throws UserNotFoundException
     * @throws TransportExceptionInterface
     */
    public function onUserRegisteredEvent(UserRegisteredEvent $event): void
    {
        $userId = $event->payload()['user_id'];

        if (!$user = $this->userRepository->find($userId)) {
            throw new UserNotFoundException();
        }

        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->subject('Welcome to Blink-Chat!')
            ->htmlTemplate('emails/auth/sign-up/welcome.html.twig')
            ->context([
                'username' => $user->getFullName()
            ]);

        $this->mailer->send($email);
    }
}
