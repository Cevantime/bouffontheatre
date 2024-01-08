<?php

namespace App\Controller;

use App\DTO\Period;
use App\Entity\Booking;
use App\Form\PeriodDTOType;
use App\Repository\BookingRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgendaController extends AbstractController
{
    #[Route('/agenda', name: 'app_agenda')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(BookingRepository $bookingRepository, Request $request): Response
    {
        $period = new Period();

        $period->from = new DateTimeImmutable();
        $period->to = $period->from->add(new DateInterval("P6M"));

        $form = $this->createForm(PeriodDTOType::class, $period);

        $form->handleRequest($request);

        $daysShow = [
            "Friday" => [["from" => "18:00", "to" => "20:15", "o_from" => "19h00", "o_to" => "20h00"], ["from" => "20:15", "to" => "23:30", "o_from" => "21h00", "o_to" => "23h00"]],
            "Saturday" => [["from" => "18:00", "to" => "20:15", "o_from" => "19h00", "o_to" => "20h00"], ["from" => "20:15", "to" => "23:30", "o_from" => "21h00", "o_to" => "23h00"]],
            "Sunday" => [["from" => "18:00", "to" => "20:15", "o_from" => "19h00", "o_to" => "20h00"], ["from" => "20:15", "to" => "23:30", "o_from" => "21h00", "o_to" => "23h00"]],
        ];

        $bookings = $bookingRepository->getEventsBetween($period->from, $period->to);

        $bookingsGroupedByDay = [];

        /** @var Booking $b */
        foreach ($bookings as $b) {
            $date = $b->getBeginAt();
            while ($date < $b->getEndAt()) {
                $day = $date->format("d/m/Y");
                if (!isset($bookingsGroupedByDay[$day])) {
                    $bookingsGroupedByDay[$day] = [];
                }

                $bookingsGroupedByDay[$day][] = $b;
                $date = $date->add(new DateInterval("P1D"));
            }
        }

        $availables = [];

        $date = $period->from;

        $showDays = array_keys($daysShow);
        $days = [];

        while ($date < $period->to) {
            $dayName = $date->format("l");
            $day = $date->format("d/m/Y");
            $days[] = $date;
            if (!in_array($dayName, $showDays)) {
                $date = $date->add(new DateInterval("P1D"));
                continue;
            }
            $dayBookings = $bookingsGroupedByDay[$day] ?? [];
            $showIntervals = $daysShow[$dayName];
            $availables[$day] = [];
            foreach ($showIntervals as $si) {
                $dateStartInterval = DateTimeImmutable::createFromFormat("d/m/Y H:i", $day . ' ' . $si['from']);
                $dateEndInterval = DateTimeImmutable::createFromFormat("d/m/Y H:i", $day . ' ' . $si['to']);
                foreach ($dayBookings as $db) {
                    if (
                        (($db->getBeginAt() >= $dateStartInterval) && ($db->getBeginAt() <= $dateEndInterval))
                        || (($db->getEndAt() >= $dateStartInterval) && ($db->getEndAt() <= $dateEndInterval))
                        || ($db->getBeginAt() <= $dateStartInterval) && ($db->getBeginAt() >= $dateEndInterval)
                    ) {
                        break 2;
                    }
                }
                $availables[$day][] = $si;
            }
            $date = $date->add(new DateInterval("P1D"));
        }

        return $this->render('front/agenda/index.html.twig', [
            'date_start' => $period->from,
            'date_end' => $period->to,
            'days' => $days,
            'availables' => $availables,
            'form' => $form->createView()
        ]);
    }
}
