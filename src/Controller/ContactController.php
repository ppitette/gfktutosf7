<?php

namespace App\Controller;

use App\Dto\contactDto;
use App\Event\ContactRequestEvent;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer, EventDispatcherInterface $dispatcher): Response
    {
        $data = new contactDto();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher->dispatch(new ContactRequestEvent($data));

                $this->addFlash('success', 'Le message a bien été envoyé.');

                return $this->redirectToRoute('contact');
            } catch (TransportExceptionInterface $e) {
                // dd($e);
                $this->addFlash('danger', 'Erreur dans l\'envoi du message');
            }
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
