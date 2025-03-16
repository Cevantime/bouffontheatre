<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResendPasswordController extends AbstractController
{
    #[IsGranted("ROLE_SUPER_ADMIN")]
    #[Route('/resend/password/{id}', name: 'app_resend_password')]
    public function index(User $user, EntityManagerInterface $manager, SessionInterface $session): Response
    {
        $user->resetPassword();
        $manager->persist($user);
        $manager->flush();
        $this->addFlash(
            'success',
            'Le mot de passe de ' . $user->getFirstname() . ' a  été régénéré'
        );
        return $this->redirectToRoute('admin_app_user_show', ['id' => $user->getId()]);
    }
}
