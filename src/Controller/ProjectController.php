<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Show;
use App\Entity\View;
use App\Repository\ViewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route(path: '/project/view/{slug}', name: 'app_project_view')]
    public function projectView(Project $project, ViewRepository $viewRepository): Response
    {
        $redirect = $project instanceof Show ? 'show' : 'service';
        return $this->redirectToRoute('app_'.$redirect.'_details', ['slug' => $project->getSlug()]);
    }
}
