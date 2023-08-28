<?php

namespace App\Controller;

use App\Calendar\Service\CalendarService;
use App\Calendar\Service\GoogleAuthentication;
use Cassandra\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(GoogleAuthentication $googleAuthentication): Response
    {
        $googleAuthentication->refreshTokenIfNeeded();
        return $this->render('front/calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }

    #[Route('/calendar/connect-to-google', name: 'app_calendar_connect_to_google')]
    public function connectToGoogle(GoogleAuthentication $googleAuthentication): Response
    {
        return $googleAuthentication->goToAuthUrl();
    }

    #[Route('/calendar/google-callback', name: 'app_calendar_google_callback')]
    public function callbackOAuth(GoogleAuthentication $googleAuthentication, Request $request): Response
    {
        $stateValid = $googleAuthentication->checkState($request->get('state'));
        $googleAuthentication->unsetState();
        if(!$stateValid) {
            throw $this->createAccessDeniedException('Invalid state');
        } elseif(!$request->get('code')) {
            throw $this->createAccessDeniedException('Code missing');
        }
        $googleAuthentication->getAccessToken($request->get('code'));
        return $this->redirectToRoute('app_calendar');
    }

    #[Route('/calendar/notifications', name: 'app_calendar_notifications')]
    public function callbackPushNotification(Request $request, CalendarService $calendarService)
    {

        $eventGoogleId = $request->headers->get('X-Goog-Resource-ID');

    }
}
