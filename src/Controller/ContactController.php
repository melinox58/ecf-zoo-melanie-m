<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        // TODO : à supprimer
        $data->name = 'John Doe';
        $data->email = 'john@doe.fr';
        $data->message = 'Super site !!';

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = (new TemplatedEmail())
                ->to('zoo.arcadia.martinon@gmail.com')  // Destinataire de l'email
                ->from($data->email)        // Expéditeur
                ->subject('Demande d\'informations')  // Sujet
                ->htmlTemplate('emails/contact.html.twig')  // Template pour l'email
                ->context([
                    'data' => $data,  // Passer les données à utiliser dans le template
                ]);

            try {
                $mailer->send($mail);  // Envoi de l'email
                $this->addFlash('success', 'Votre mail a bien été envoyé');
                return $this->redirectToRoute('app_home'); // Redirigez vers la page d'accueil
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre mail');
            }
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, on affiche le formulaire
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
