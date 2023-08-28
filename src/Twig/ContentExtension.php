<?php

namespace App\Twig;

use App\Entity\Content;
use App\Repository\ContentRepository;
use Exception;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    private ContentRepository $contentRepository;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(ContentRepository $contentRepository, PropertyAccessorInterface $propertyAccessor)
    {
        $this->contentRepository = $contentRepository;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_content', [$this, 'getContent']),
        ];
    }

    public function getContent($contentSlug)
    {
        $content = $this->contentRepository->getContentBySlug($contentSlug);
        if (!$content) {
            throw new Exception("Content not found : " . $contentSlug);
        }
        return $this->propertyAccessor->getValue($content, $content->getType());
    }
}
