<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }
   #[Route(path: '/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('front/home/index.html.twig');
    }
}
