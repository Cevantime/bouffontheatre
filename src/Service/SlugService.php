<?php

namespace App\Service;

use App\Entity\Artist;
use App\Entity\BlogPost;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugService
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

    public function generatePostSlug(BlogPost $post)
    {
        $post->setSlug(strtolower($this->slugger->slug($post->getTitle())));
    }
}
