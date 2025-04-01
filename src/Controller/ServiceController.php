<?php

namespace App\Controller;

use App\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceController extends AbstractController
{
    #[Route(path: '/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('front/service/index.html.twig');
    }

    #[Route(path: '/service/{slug}', name: 'app_service_details')]
    public function details(Service $service): Response
    {
        return $this->render('front/service/details.html.twig', [
            'service' => $service,
        ]);
    }
}
