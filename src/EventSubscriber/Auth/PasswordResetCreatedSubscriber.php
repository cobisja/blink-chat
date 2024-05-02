<?php

namespace App\EventSubscriber\Auth;

use App\Event\Auth\PasswordResetCreatedEvent;
use App\Exception\Auth\PasswordResetNotFound;
use App\Repository\PasswordResetRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class PasswordResetCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetCreatedEvent::class => 'onPasswordResetCreatedEvent',
        ];
    }

    public function __construct(
        private MailerInterface $mailer,
        private PasswordResetRepository $passwordResetRepository
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws PasswordResetNotFound
     */
    public function onPasswordResetCreatedEvent(PasswordResetCreatedEvent $event): void
    {
        $email = $event->payload()['email'];

        if (!$passwordReset = $this->passwordResetRepository->findByEmail($email)) {
            throw new PasswordResetNotFound();
        }

        $email = (new TemplatedEmail())
            ->to(new Address($email))
            ->subject('Reset password instructions')
            ->htmlTemplate('emails/auth/passwords/password_reset_requested.html.twig')
            ->context([
                'user_email' => $passwordReset->getEmail(),
                'reset_token' => $passwordReset->getToken(),
                'valid_until' => $passwordReset->getValidUntil(),
            ]);

        $this->mailer->send($email);
    }
}
