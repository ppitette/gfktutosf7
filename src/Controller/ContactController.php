<?php

namespace App\Controller;

use App\Dto\contactDto;
use App\Form\ContactType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new contactDto();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mail = new TemplatedEmail()
                ->to($data->service)
                ->from(new Address($data->email, $data->name))
                ->subject('Demande de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data])
            ;

            try {
                $mailer->send($mail);
                $this->addFlash('success', 'Le message a bien été envoyé.');
                return $this->redirectToRoute('contact');
            } catch (TransportExceptionInterface $e) {
                //dd($e);
                $this->addFlash('danger', 'Erreur dans l\'envoi du message');
            }
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
