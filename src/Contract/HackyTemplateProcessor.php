<?php

namespace App\Contract;

use PhpOffice\PhpWord\TemplateProcessor;
use Twig\Environment;

class HackyTemplateProcessor extends TemplateProcessor
{


    public function __construct(
        string $documentTemplate,
        private Environment $twig
    )
    {
        parent::__construct($documentTemplate);
    }

    public function saveAsWithCallbackReplacement($output, $pattern, $callback)
    {
        $oldMainPart = $this->tempDocumentMainPart;
        $this->tempDocumentMainPart = preg_replace_callback($pattern, $callback, $this->tempDocumentMainPart);
        $this->saveAs($output);
        $this->tempDocumentMainPart = $oldMainPart;
    }

    public function saveAsWithTwigMainPart($output, $mainPartTemplate, $context)
    {
        $oldMainPart = $this->tempDocumentMainPart;
        $this->tempDocumentMainPart = $this->twig->render($mainPartTemplate, $context);
        $this->saveAs($output);
        $this->tempDocumentMainPart = $oldMainPart;
    }

    public function generateMainPartXML($output)
    {
        file_put_contents($output, $this->tempDocumentMainPart);
    }
}
