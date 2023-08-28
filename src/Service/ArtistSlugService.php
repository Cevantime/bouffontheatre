<?php

namespace App\Service;

use App\Entity\Artist;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArtistSlugService
{
    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function generateArtistSlug(Artist $artist)
    {
        $artist->setSlug(strtolower($this->slugger->slug($artist->getFullname())));
    }
}
