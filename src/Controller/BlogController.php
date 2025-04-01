<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        return $this->render('front/blog/index.html.twig');
    }

    #[Route('/blog/{slug}', name: 'app_blog_details')]
    public function details(BlogPost $post): Response
    {
        return $this->render('front/blog/details.html.twig', [
            'post' => $post,
        ]);
    }
}
