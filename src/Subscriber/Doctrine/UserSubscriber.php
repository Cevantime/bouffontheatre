<?php

namespace App\Subscriber\Doctrine;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $hasher;
    private MailerInterface $mailer;

    public function __construct(UserPasswordHasherInterface $hasher, MailerInterface $mailer)
    {
        $this->hasher = $hasher;
        $this->mailer = $mailer;
    }

    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $user = $args->getObject();

        if (!($user instanceof User)) {
            return;
        }

        if ($user->getPassword() === null) {
            $plainPassword = $this->generateRandomString(8);
            $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
            $user->setIsVerified(true);
            $mail = (new TemplatedEmail())
                ->from('contactbouffon@gmail.com')
                ->to(new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()))
                ->subject('Vous avez été ajouté à la plateforme bouffontheatre.fr')
                ->htmlTemplate('front/user/email_user_admin_register.html.twig')
                ->context(['user' => $user, 'plain_password' => $plainPassword]);

            $this->mailer->send($mail);
        }

        if (!$user->hasRole(User::ROLE_ARTIST) && $user->getAssociatedArtist() !== null) {
            $user->addRole(User::ROLE_ARTIST);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $user = $args->getObject();

        if (!($user instanceof User)) {
            return;
        }

        if ($user->getPassword() === null) {
            $plainPassword = $this->generateRandomString(8);
            $user->setPassword($this->hasher->hashPassword($user, $plainPassword));

            $mail = (new TemplatedEmail())
                ->from('contactbouffon@gmail.com')
                ->to(new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()))
                ->subject('Votre mot de passe a été régénéré sur la plateforme bouffontheatre.fr')
                ->htmlTemplate('resend_password/email.html.twig')
                ->context(['user' => $user, 'plain_password' => $plainPassword]);

            $this->mailer->send($mail);
        }
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
