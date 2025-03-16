<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileType;
use App\Repository\DownloadRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profile', name: 'app_profile')]
    public function index(ViewRepository $viewRepository, DownloadRepository $downloadRepository, Request $request, EntityManagerInterface $manager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $downloads = $downloadRepository->getDownloadsWithMedia($user);

        $formProfile = $this->createForm(ProfileType::class, $user);
        $formPassword = $this->createForm(ChangePasswordFormType::class, $user);

        foreach ([$formProfile, $formPassword] as $form) {
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);
                $manager->flush();
            }
        }

        return $this->render('front/profile/index.html.twig', [
            'downloads' => $downloads,
            'formProfile' => $form->createView(),
            'formPassword' => $formPassword->createView()
        ]);
    }
}
