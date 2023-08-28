<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/booking')]
class BookingController extends AbstractController
{

    #[Route('/new', name: 'app_booking_new', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function new(Request $request, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();

        if ($request->get('dateStart') !== null) {
            $dateStart = DateTimeImmutable::createFromFormat("d/m/Y H:i:s", $request->get('dateStart'));
            $dateEnd = DateTimeImmutable::createFromFormat("d/m/Y H:i:s", $request->get('dateEnd'));
            $booking->setBeginAt($dateStart);
            $booking->setEndAt($dateEnd);
        }

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingRepository->add($booking, true);

            return $this->json(['status' => 'ok', 'message' => 'booking created'], Response::HTTP_CREATED);
        }

        return $this->renderForm('front/booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        return $this->render('front/booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/update', name: 'app_booking_update', methods: ['POST'], options: ['expose' => true])]
    public function update(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        $dateStart = DateTimeImmutable::createFromFormat("d/m/Y H:i:s", $request->get('dateStart'));
        $dateEnd = DateTimeImmutable::createFromFormat("d/m/Y H:i:s", $request->get('dateEnd'));
        $booking->setBeginAt($dateStart);
        $booking->setEndAt($dateEnd);
        $bookingRepository->add($booking, true);
        return $this->json(['status' => 'ok', 'message' => 'booking updated'], Response::HTTP_ACCEPTED);
    }

    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function edit(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingRepository->add($booking, true);

            return $this->json(['status' => 'ok', 'message' => 'booking updated'], Response::HTTP_ACCEPTED);
        }

        return $this->renderForm('front/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_booking_delete', methods: ['POST'])]
    public function delete(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $booking->getId(), $request->request->get('_token'))) {
            $bookingRepository->remove($booking, true);
        }

        return $this->json(['status' => 'ok', 'booking deleted'], Response::HTTP_ACCEPTED);
    }
}
