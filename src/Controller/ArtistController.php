<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\User;
use App\Entity\View;
use App\Form\BasicArtistType;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController extends AbstractController
{
    #[Route(path: '/artist', name: 'app_artist')]
    public function index(): Response
    {
        return $this->render('front/artist/index.html.twig');
    }

    #[IsGranted('ARTIST_VIEW_PROFILE', subject: 'artist')]
    #[Route(path: '/artist/{slug}', name: 'app_artist_details')]
    public function details(Artist $artist)
    {
        return $this->render('front/artist/details.html.twig', [
            'artist' => $artist
        ]);
    }

    #[IsGranted('ARTIST_VIEW_PROFILE', subject: 'artist')]
    #[Route(path: '/artist/view/{slug}', name: 'app_artist_view')]
    public function artistView(Artist $artist, ViewRepository $viewRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            $view = $viewRepository->findOneBy(['user' => $user, 'artist' => $artist]);
            if (!$view) {
                $view = new View();
                $view->setUser($user);
                $view->setArtist($artist);
                $viewRepository->add($view, true);
            }
        }
        return $this->redirectToRoute('app_artist_details', ['slug' => $artist->getSlug()]);
    }

    #[IsGranted('ARTIST_CREATE')]
    #[Route(path: '/artist/create/ajax', name: 'app_artist_create_ajax', options: ['expose' => true])]
    public function createAjax(Request $request, EntityManagerInterface $entityManager)
    {
        $artist = new Artist();
        $payload = $request->getPayload();
        if( ! empty($payload->get('firstName') && ! empty($payload->get('lastName')))) {
            $artist->setFirstname($payload->get('firstName'));
            $artist->setLastname($payload->get('lastName'));
            $entityManager->persist($artist);
            $entityManager->flush();
            return $this->json(['id'=> $artist->getId(), 'fullName' => $artist->getFullname(), 'status' => 'success']);
        }

        throw $this->createNotFoundException();
    }
}
