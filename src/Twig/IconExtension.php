<?php

namespace App\Twig;

use App\Entity\Link;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class IconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'generateIconTag'], ['is_safe' => ['html']]),
            new TwigFunction('link_icon', [$this, 'generateLinkIconTag'], ['is_safe' => ['html']]),
        ];
    }

    public function generateIconTag($siteName)
    {
        return '<i class="icofont-'.$siteName.'"></i>';
    }
    public function generateLinkIconTag(Link $link)
    {
        return '<a class="social-link" href="'.$link->getUrl().'" title="'.$link->getTitle().'">' .$this->generateIconTag($link->getSiteName()).'</a>';
    }
}
