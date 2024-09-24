<?php

namespace App\Controller;

use App\Entity\Show;
use App\Repository\InsightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    #[Route(path: '/show', name: 'app_show')]
    public function index(): Response
    {
        return $this->render('front/show/index.html.twig');
    }

    #[Route(path: '/other', name: 'app_other')]
    public function otherShows(): Response
    {
        return $this->render('front/show/other.html.twig');
    }

    #[Route(path: '/show/{slug}', name: 'app_show_details')]
    public function details(Show $show, InsightRepository $insightRepository): Response
    {
        $insight = $insightRepository->findOneBy(['relatedShow' => $show]);

        return $this->render('front/show/details.html.twig', [
            'show' => $show,
            'there_are_insights_for_this_show' => $insight !== null
        ]);
    }
}
