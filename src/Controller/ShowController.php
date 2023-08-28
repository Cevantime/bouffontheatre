<?php

namespace App\Controller;

use App\Entity\Show;
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

    #[Route(path: '/show/{slug}', name: 'app_show_details')]
    public function details(Show $show): Response
    {
        return $this->render('front/show/details.html.twig', [
            'show' => $show,
        ]);
    }
}
