<?php

namespace App\Controller;

use App\Entity\Performance;
use App\Entity\Reservation;
use App\Entity\Show;
use App\Form\ReservationEditType;
use App\Form\ReservationType;
use App\Repository\PerformanceRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/reservation")]
class ReservationController extends AbstractController
{
    #[Route('/{slug}', name: 'app_reservation')]
    public function index(Show $show, PerformanceRepository $performanceRepository): Response
    {
        $availablePerformances = $performanceRepository->findAvailablePerformancesForShowWithReservations($show);
        return $this->render('front/reservation/index.html.twig', [
            'available_performances' => $availablePerformances,
            'show' => $show,
        ]);
    }

    #[Route('/form/{id}', name: 'app_reservation_form')]
    public function form(Performance $performance, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$performance->isAvailableForReservation()) {
            $this->addFlash("danger", "Cette représentation n'est plus disponible à la réservation");
            return $this->redirectToRoute("app_reservation", ['slug' => $performance->getRelatedProject()->getSlug()]);
        }

        $reservation = new Reservation();
        $reservation->setPerformance($performance);

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute("app_reservation_success", [
                'id' => $performance->getId(),
                'idReservation' => $reservation->getId()
            ]);
        }

        return $this->render('front/reservation/form.html.twig', [
            'form' => $form->createView(),
            'performance' => $performance
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/edit/{id}', name: 'app_reservation_edit')]
    public function edit(Reservation $reservation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $performance = $reservation->getPerformance();
        if ($performance->hasExpired()) {
            $this->addFlash("danger", "Cette représentation a expiré");
            return $this->redirectToRoute("app_reservation", ['slug' => $performance->getRelatedProject()->getSlug()]);
        }

        $form = $this->createForm(ReservationEditType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute("app_reservation_success", [
                'id' => $performance->getId(),
                'idReservation' => $reservation->getId()
            ]);
        }

        return $this->render('front/reservation/form.html.twig', [
            'form' => $form->createView(),
            'performance' => $performance
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/delete/{id}', name: 'app_reservation_delete')]
    public function delete(Reservation $reservation, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($reservation);
        $entityManager->flush();
        return $this->redirectToRoute('app_reservation_view', ['id' => $reservation->getPerformance()->getId()]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/view/{id}', name: 'app_reservation_view')]
    public function view(Performance $performance): Response
    {
        return $this->render('front/reservation/view.html.twig', [
            'performance' => $performance
        ]);
    }

    #[Route('/reservation-success/{id}/{idReservation}', name: 'app_reservation_success')]
    public function success(Performance $performance, ReservationRepository $reservationRepository, $idReservation): Response
    {
        $reservation = $reservationRepository->find($idReservation);

        return $this->render('front/reservation/success.html.twig', [
            'performance' => $performance,
            'reservation' => $reservation
        ]);
    }
}
