<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\DownloadRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $views = $viewRepository->getUserViewsWithArtistAndProject($user);
        $downloads = $downloadRepository->getDownloadsWithMedia($user);

        $formProfile = $this->createForm(ProfileType::class, $user);

        $formProfile->handleRequest($request);

        if($formProfile->isSubmitted() && $formProfile->isValid()) {
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('front/profile/index.html.twig', [
            'views' => $views,
            'downloads' => $downloads,
            'formProfile' => $formProfile->createView()
        ]);
    }
}
