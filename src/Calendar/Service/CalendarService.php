<?php

namespace App\Calendar\Service;

use _PHPStan_7c8075089\Nette\Neon\Exception;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CalendarService
{

    const API_URL = 'https://www.googleapis.com/calendar/v3/calendars/';
    const EVENT_END_POINT = 'events';
    const EVENT_INSTANCES_END_POINT = "events/%s/instances";

    private string $apiUrl;
    private Client $guzzleClient;

    public function __construct(
        private ColorService  $colorService,
        private ConfigService $config,
        private BookingRepository $bookingRepository,
        private EntityManagerInterface $manager,
        ParameterBagInterface $parameterBag
    ) {
        $this->apiUrl = self::API_URL . $parameterBag->get('google_email') . '/';
        $this->guzzleClient = new Client();
    }

    private function makeRequest($method, $uri, $headers = [], $body = null, $version = '1.1'): RequestInterface
    {
        if (!$this->config->hasValue(ConfigService::ACCESS_TOKEN)) {
            throw new \Exception('Unable to connect to Google API. The app is not connected yet.');
        }
        /** @var RequestInterface $request */
        $request = (new Request($method, $this->apiUrl . $uri, $headers, $body, $version))
            ->withHeader('Authorization', 'Bearer ' . $this->config->getValue(ConfigService::ACCESS_TOKEN));
        return $request;
    }

    private function makeJsonRequest($json, $method, $uri, $headers = [], $version = '1.1')
    {
        return $this->makeRequest($method, $uri, $headers, null, $version)
            ->withBody(Utils::streamFor(json_encode($json)))
            ->withHeader('Content-Type', 'application/json');
    }

    private function getJson(RequestInterface $request)
    {
        $response = $this->guzzleClient->send($request);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 400) {
            throw new \Exception('Resource not found');
        }
        return json_decode($response->getBody());
    }

    public function getEvents($maxResults = 250, $pageToken = null)
    {
        $query = ['maxResults' => $maxResults];
        if ($pageToken) {
            $query['pageToken'] = $pageToken;
        }
        return $this->getJson($this->makeRequest('GET', self::EVENT_END_POINT . '?' . http_build_query($query)));
    }

    public function persistBookingFromGoogleEvent($googleEvent, $isInstance = false)
    {
        $booking = $this->bookingRepository->findOneBy(['googleId' => $googleEvent->id]);
        if (!$booking) {
            $booking = new Booking();
        }
        $booking->setType($this->colorService->findTypeFromGoogleColorId($googleEvent->colorId ?? 0));
        $booking->setBeginAt($this->parseDate($googleEvent->start));
        $booking->setEndAt($this->parseDate($googleEvent->end));
        $booking->setTitle($googleEvent->summary ?? 'Inconnu');
        $booking->setGoogleId($googleEvent->id);
        $booking->setIsInstance($isInstance);
        $this->manager->persist($booking);
        return $booking;
    }

    public function syncBookings()
    {
        $unsyncedBooking = $this->bookingRepository->findBy(['googleId' => null]);
        foreach ($unsyncedBooking as $booking) {
            $this->syncBooking($booking);
        }
    }

    public function syncBookingFromGoogleEvent($googleEvent)
    {
        $this->persistBookingFromGoogleEvent($googleEvent);
        $this->manager->flush();
    }

    public function getGoogleEvent($googleId)
    {
        $request = $this->makeRequest('GET', self::EVENT_END_POINT . '/' . $googleId);
        return $this->getJson($request);
    }

    public function getRecurringEventInstances($googleId)
    {
        $request = $this->makeRequest('GET', sprintf(self::EVENT_INSTANCES_END_POINT, $googleId));
        $result = $this->getJson($request);
        return $result->items;
    }

    public function syncBooking(Booking $booking)
    {
        if ($booking->getGoogleId() !== null) {
            $this->patchGoogleEventFromBooking($booking);
        } else {
            $this->createGoogleEventFromBooking($booking);
        }
    }

    public function patchGoogleEventFromBooking(Booking $booking)
    {
        $request = $this->makeJsonRequest($this->bookingToEvent($booking), 'PUT', self::EVENT_END_POINT . '/' . $booking->getGoogleId());
        return $this->guzzleClient->send($request);
    }

    public function createGoogleEventFromBooking(Booking $booking)
    {
        $request = $this->makeJsonRequest($this->bookingToEvent($booking), 'POST', self::EVENT_END_POINT);
        $response = $this->guzzleClient->send($request);
        $event = json_decode($response->getBody());
        $booking->setGoogleId($event->id);
        $this->manager->persist($booking);
        $this->manager->flush();
    }

    public function deleteAssociatedGoogleEvent(Booking $booking)
    {

        $request = $this->makeRequest('DELETE', self::EVENT_END_POINT . '/' . $booking->getGoogleId());

        return $this->guzzleClient->send($request);
    }

    public function syncEvents()
    {
        $nextSyncToken = $this->config->hasKey(ConfigService::EVENT_NEXT_SYNC_TOKEN)
            ? $this->config->getValue(ConfigService::EVENT_NEXT_SYNC_TOKEN)
            : null;
        do {
            $params = [];
            if ($nextSyncToken) {
                $params['syncToken'] = $nextSyncToken;
            }
            if (isset($nextPageToken)) {
                $params['pageToken'] = $nextPageToken;
            }
            $request = $this->makeRequest('GET', self::EVENT_END_POINT . '?' . http_build_query($params));
            $result = $this->getJson($request);
            $nextSyncToken = $result->nextSyncToken ?? null;
            $nextPageToken = $result->nextPageToken ?? null;
            $events = $result->items;
            foreach ($events as $event) {
                if ($event->status === 'cancelled') {
                    $this->deleteBookingsAssociatedWithEvent($event->id);
                } else {
                    if (isset($event->recurrence)) {
                        $this->deleteBookingsAssociatedWithEvent($event->id);
                        $instances = $this->getRecurringEventInstances($event->id);
                        foreach ($instances as $instance) {
                            $this->persistBookingFromGoogleEvent($instance, true);
                        }
                    } else {
                        $this->persistBookingFromGoogleEvent($event);
                    }
                }
            }
            $this->manager->flush();
        } while ($nextPageToken);
        $this->config->saveConfig(ConfigService::EVENT_NEXT_SYNC_TOKEN, $nextSyncToken);
    }

    private function deleteBookingsAssociatedWithEvent($eventId)
    {
        $bookings = $this->bookingRepository->findBookingsByEventId($eventId);
        foreach ($bookings as $booking) {
            $this->manager->remove($booking);
        }
        $this->manager->flush();
    }

    private function bookingToEvent(Booking $booking)
    {
        return [
            'summary' => $booking->getTitle(),
            'end' => [
                'dateTime' => $booking->getEndAt()->format(\DateTime::RFC3339),
                'timeZone' => $booking->getEndAt()->getTimezone()->getName()
            ],
            'start' => [
                'dateTime' => $booking->getBeginAt()->format(\DateTime::RFC3339),
                'timeZone' => $booking->getBeginAt()->getTimezone()->getName()
            ],
            'colorId' => $this->colorService->getBookingGoogleColorId($booking)
        ];
    }
    private function parseDate($googleDate)
    {
        if (isset($googleDate->dateTime)) {
            return \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $googleDate->dateTime);
        } else {
            return \DateTimeImmutable::createFromFormat('Y-m-d', $googleDate->date);
        }
    }
}
