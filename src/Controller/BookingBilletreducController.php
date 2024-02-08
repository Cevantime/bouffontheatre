<?php

namespace App\Controller;

use App\Entity\Show;
use App\Repository\InsightRepository;
use App\Security\Voter\BookingBilletreducVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingBilletreducController extends AbstractController
{
    #[Route('/booking/billetreduc/{id}', name: 'app_booking_billetreduc')]
    #[IsGranted('ROLE_INSIGHT_SHOW_VIEW', subject: 'show')]
    public function viewBilletreducBookingCount(Show $show, InsightRepository $insightRepository): Response
    {
        $insights = $insightRepository->findBy(['relatedShow' => $show]);

        if (!$insights) {
            throw $this->createNotFoundException();
        }

        return $this->render('front/booking_billetreduc/view_billetreduc_booking_count.html.twig', [
            'insights' => $insights,
        ]);
    }
}
