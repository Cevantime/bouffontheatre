<?php

namespace App\Subscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ParameterBagInterface $params,
        private Security $security,
        private UrlGeneratorInterface $urlGenerator

    )
    {
    }

    public function onKerneRequest(RequestEvent $event): void
    {
        if($this->params->get('maintenance_mode') == 0) {
            return;
        }
        if($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }
        $route = $event->getRequest()->attributes->get('_route');
        if($route !== 'app_login') {
            $event->getRequest()->getSession()->getFlashBag()->add('info', 'Le site en mode maintenance. Veuillez vous authentifier en tant qu\'adminitrateur pour y accéder');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_login')));
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKerneRequest',
        ];
    }
}
