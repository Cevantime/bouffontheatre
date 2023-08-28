<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LinkActiveExtension extends AbstractExtension
{
    private ?Request $mainRequest;

    public function __construct(RequestStack $requestStack)
    {
        $this->mainRequest = $requestStack->getMainRequest();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_class', [$this, 'activeClass']),
        ];
    }

    public function activeClass($routes)
    {
        if( is_string($routes)) {
            $routes = [$routes];
        }
        return in_array($this->mainRequest->get('_route'), $routes) ? ' active' : '';
    }
}
