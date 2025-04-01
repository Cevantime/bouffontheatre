<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\WorkflowSectionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WorkflowSectionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('workflow_section_start', [WorkflowSectionRuntime::class, 'workflowSectionStart']),
            new TwigFunction('workflow_section_end', [WorkflowSectionRuntime::class, 'workflowSectionEnd']),
        ];
    }
}
