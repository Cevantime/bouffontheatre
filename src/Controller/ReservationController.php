<?php

namespace App\Controller;

use App\Entity\Performance;
use App\Entity\Reservation;
use App\Entity\Show;
use App\Form\ReservationEditType;
use App\Form\ReservationType;
use App\Repository\PerformanceRepository;
use App\Repository\ReservationRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/reservation")]
class ReservationController extends AbstractController
{
    #[IsGranted('SHOW_BOOK_ONLINE', subject: 'show')]
    #[Route('/{slug}', name: 'app_reservation')]
    public function index(Show $show, PerformanceRepository $performanceRepository): Response
    {
        $availablePerformances = $performanceRepository->findAvailablePerformancesForShowWithReservations($show);
        return $this->render('front/reservation/index.html.twig', [
            'available_performances' => $availablePerformances,
            'show' => $show,
        ]);
    }

    #[IsGranted('RESERVATION_ADD_TO_PERFORMANCE', subject: 'performance')]
    #[Route('/form/{id}', name: 'app_reservation_form')]
    public function form(Performance $performance, Request $request, EntityManagerInterface $entityManager, EmailService $emailService): Response
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
            $emailService->sendMailTo(
                $reservation->getEmail(),
                'Votre réservation a bien été enregistrée',
                'front/reservation/email_reservation_done.html.twig',
                ['reservation' => $reservation]
            );
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute("app_reservation_success", [
                'id' => $reservation->getId()
            ]);
        }

        return $this->render('front/reservation/form.html.twig', [
            'form' => $form->createView(),
            'performance' => $performance
        ]);
    }

    #[IsGranted("RESERVATION_EDIT", subject: 'reservation')]
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
                'id' => $reservation->getId()
            ]);
        }

        return $this->render('front/reservation/form.html.twig', [
            'form' => $form->createView(),
            'performance' => $performance
        ]);
    }

    #[IsGranted("RESERVATION_DELETE", subject: 'reservation')]
    #[Route('/delete/{id}', name: 'app_reservation_delete')]
    public function delete(Reservation $reservation, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($reservation);
        $entityManager->flush();
        return $this->redirectToRoute('app_reservation_view', ['id' => $reservation->getPerformance()->getId()]);
    }

    #[Route('/view/{id}', name: 'app_reservation_view')]
    public function view(Performance $performance): Response
    {
        if( ! $this->isGranted('SHOW_LIST_RESERVATION', $performance->getRelatedProject())) {
            throw $this->createAccessDeniedException();
        }
        $sortedReservation = $performance->getReservations()->toArray();
        usort($sortedReservation, function(Reservation $r1, Reservation $r2){
            return $r1->getLastName() <=> $r2->getLastName();
        });
        return $this->render('front/reservation/view.html.twig', [
            'performance' => $performance,
            'reservations' => $sortedReservation
        ]);
    }

    #[Route('/reservation-success/{id}', name: 'app_reservation_success')]
    public function success(Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        return $this->render('front/reservation/success.html.twig', [
            'performance' => $reservation->getPerformance(),
            'reservation' => $reservation
        ]);
    }
}
