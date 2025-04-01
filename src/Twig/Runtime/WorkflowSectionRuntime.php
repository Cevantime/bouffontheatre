<?php

namespace App\Twig\Runtime;

use App\Service\WorkflowService;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

class WorkflowSectionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private WorkflowService $workflowService)
    {
        // Inject dependencies if needed
    }

    public function workflowSectionStart($workflow, $sectionId)
    {
        $classes = [];

        if( ! $this->workflowService->workflowCanAccess($workflow, $sectionId)){
            $classes[] = 'workflow-section-hidden';
        }

        if($this->workflowService->workflowValidated($workflow, $sectionId)){
            $classes[] = 'workflow-section-success';
        }

        return new Markup(sprintf('<section class="box box-primary workflow-section %s" id="%s">', implode(' ', $classes), $sectionId), 'UTF-8');
    }

    public function workflowSectionEnd()
    {
        return new Markup('</section>', 'UTF-8');
    }
}
