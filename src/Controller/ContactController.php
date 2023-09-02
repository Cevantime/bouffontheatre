<?php

namespace App\Controller;

use App\DTO\Contact;
use App\Entity\User;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route(path: '/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        if($this->getUser()) {
            /** @var User $user */
            $user = $this->getUser();
            $contact->lastname = $user->getLastname();
            $contact->firstname = $user->getFirstname();
            $contact->email = $user->getEmail();
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $mail = (new TemplatedEmail())
                ->from(new Address($contact->email, $contact->firstname.' '.$contact->lastname))
                ->to('contactbouffon@gmail.com',)
                ->subject($contact->firstname.' '.$contact->lastname.' vous a contacté via bouffontheatre.fr')
                ->htmlTemplate('front/contact/email_contact.html.twig')
                ->context(['contact' => $contact])
            ;

            $mailer->send($mail);
            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('front/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
