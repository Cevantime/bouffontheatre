<?php

namespace App\Twig;

use App\Entity\Project;
use App\Entity\Show;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProjectExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_project_bookable', [$this, 'isProjectBookable']),
        ];
    }

    public function isProjectBookable(Project $project): bool
    {
        if( ! ($project instanceof Show)) {
            return false;
        }
        return $project->canBeBooked();
    }
}
