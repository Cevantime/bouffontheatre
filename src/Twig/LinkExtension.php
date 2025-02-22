<?php

namespace App\Twig;

use App\Entity\Artist;
use App\Entity\Link;
use App\Entity\LinkItem;
use App\Entity\Media;
use App\Security\Voter\ArtistVoter;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LinkExtension extends AbstractExtension
{
    private RouterInterface $router;
    private Security $security;

    public function __construct(RouterInterface $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('link', [$this, 'link'], ['is_safe' => ['html']]),
        ];
    }

    public function link($value)
    {
        if($value instanceof  LinkItem) {
            $value = $value->getLink();
        }
        if($value instanceof Link) {
            return $this->generateLinkLinkTag($value);
        } elseif ($value instanceof Artist) {
            return $this->generateArtistLinkTag($value);
        } else {
            return $value;
        }
    }

    private function generateLinkTag($href, $content, $title = null)
    {
        if(!$title) {
            $title = 'Accéder à '.$href;
        }
        return '<a href="'.$href.'" title="'.$title.'">'.$content.'</a>';
    }

    private function generateArtistLinkTag(Artist $artist)
    {
        if( ! $this->security->isGranted(ArtistVoter::VIEW_PROFILE, $artist)) {
            return $artist->getFullname();
        }
        $url = $this->router->generate('app_artist_view', ['slug' => $artist->getSlug()]);
        return $this->generateLinkTag($url, $artist->getFullname(), 'Accéder au profile de '.$artist->getFullname());
    }

    private function generateLinkLinkTag(Link $link)
    {
        return $this->generateLinkTag($link->getUrl(), $link->getTitle());
    }
}
