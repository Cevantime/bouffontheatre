<?php

namespace App\Controller;

use App\Calendar\Service\CalendarService;
use App\Calendar\Service\GoogleAuthenticationService;
use Cassandra\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('front/calendar/index.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/calendar/connect-to-google', name: 'app_calendar_connect_to_google')]
    public function connectToGoogle(GoogleAuthenticationService $googleAuthentication): Response
    {
        return $googleAuthentication->goToAuthUrl();
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/calendar/google-callback', name: 'app_calendar_google_callback')]
    public function callbackOAuth(GoogleAuthenticationService $googleAuthentication, Request $request, CalendarService $calendarService): Response
    {
        $stateValid = $googleAuthentication->checkState($request->get('state'));
        $googleAuthentication->unsetState();
        if (!$stateValid) {
            throw $this->createAccessDeniedException('Invalid state');
        } elseif (!$request->get('code')) {
            throw $this->createAccessDeniedException('Code missing');
        }
        $googleAuthentication->grabAccessToken($request->get('code'));

        $calendarService->sync();

        return $this->redirectToRoute('app_calendar');
    }
}
