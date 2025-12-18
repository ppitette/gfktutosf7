<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        public readonly MailerInterface $mailer,
    ) {
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;

        $mail = new TemplatedEmail()
            ->to($data->service)
            ->from(new Address($data->email, $data->name))
            ->subject('Demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['data' => $data])
        ;

        $this->mailer->send($mail);
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $mail = new Email()
            ->to($user->getEmail())
            ->from('support@tuto.fr')
            ->subject('Connexion réussie')
            ->text('Vous êtes bien connecté.')
        ;

        $this->mailer->send($mail);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
