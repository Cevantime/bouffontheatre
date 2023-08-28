<?php

namespace App\Crop;

use Gaufrette\File;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\ResizerInterface;

class CropResizer implements ResizerInterface
{
    private ImagineInterface $adapter;

    private MetadataBuilderInterface $metadata;

    public function __construct(ImagineInterface $adapter, MetadataBuilderInterface $metadata)
    {
        $this->adapter = $adapter;
        $this->metadata = $metadata;
    }

    public function resize(MediaInterface $media, File $in, File $out, string $format, array $settings): void
    {
        if (!isset($settings['width'])) {
            throw new \InvalidArgumentException(sprintf(
                'The "width" parameter is missing in context "%s" for provider "%s".',
                $media->getContext() ?? '',
                $media->getProviderName() ?? ''
            ));
        }

        if (!isset($settings['height'])) {
            throw new \InvalidArgumentException(sprintf(
                'The "height" parameter is missing in context "%s" for provider "%s".',
                $media->getContext() ?? '',
                $media->getProviderName() ?? ''
            ));
        }

        $image = $this->adapter->load($in->getContent());

        $sourceSize = $media->getBox();
        $targetSize = $this->createTargetBox($settings);

        if ($this->shouldModify($sourceSize, $targetSize)) {
            $image = $this->cropImage($image, $sourceSize, $targetSize);
        }

        // Always change format and quality
        $content = $image->get($format, [
            'quality' => $settings['quality'],
        ]);

        $out->setContent($content, $this->metadata->get($media, $out->getName()));
    }

    public function getBox(MediaInterface $media, array $settings): Box
    {
        $sourceSize = $media->getBox();
        $targetSize = $this->createTargetBox($settings);

        return new Box(
            min($sourceSize->getWidth(), $targetSize->getWidth()),
            min($sourceSize->getHeight(), $targetSize->getHeight())
        );
    }

    /**
     * @param array<string, int|string|bool|array|null>|false $settings
     *
     * @phpstan-param FormatOptions $settings
     */
    private function createTargetBox(array $settings): Box
    {
        return new Box($settings['width'] ?? 0, $settings['height'] ?? 0);
    }

    private function shouldModify(Box $sourceSize, Box $targetSize): bool
    {
        return !($sourceSize->getWidth() == $targetSize->getWidth() && $sourceSize->getHeight() == $targetSize->getHeight());
    }

    private function shouldResize(Box $sourceSize, Box $targetSize): bool
    {
        return $sourceSize->getHeight() != $targetSize->getHeight() || $sourceSize->getWidth() != $targetSize->getWidth();
    }

    private function shouldCrop(Box $sourceSize, Box $targetSize): bool
    {
        return ($sourceSize->getWidth() / $sourceSize->getHeight()) != ($targetSize->getWidth() / $targetSize->getHeight());
    }

    private function cropImage(ImageInterface $image, Box $sourceSize, Box $targetSize): ImageInterface
    {
        if ($this->shouldResize($sourceSize, $targetSize)) {
            $scaleSize = $this->createBox($sourceSize, $targetSize, false);

            $image = $image->thumbnail($scaleSize, ImageInterface::THUMBNAIL_OUTBOUND + ImageInterface::THUMBNAIL_FLAG_UPSCALE);

            $sourceSize = $scaleSize;
        }

        if ($this->shouldCrop($sourceSize, $targetSize)) {
            $cropSize = new Box(
                min($sourceSize->getWidth(), $targetSize->getWidth()),
                min($sourceSize->getHeight(), $targetSize->getHeight())
            );

            $point = new Point(
                (int) (($sourceSize->getWidth() - $cropSize->getWidth()) / 2),
                (int) (($sourceSize->getHeight() - $cropSize->getHeight()) / 2)
            );

            $image = $image->crop($point, $cropSize);
        }

        return $image;
    }

    private function createBox(Box $sourceSize, Box $targetSize, bool $smallest = true): Box
    {
        $widthRatio = (float) ($targetSize->getWidth() / $sourceSize->getWidth());
        $heightRatio = (float) ($targetSize->getHeight() / $sourceSize->getHeight());

        if (0.0 !== $widthRatio - $heightRatio) {
            return $sourceSize->scale(
                $smallest ? min($widthRatio, $heightRatio) : max($widthRatio, $heightRatio)
            );
        }

        if ($targetSize->getHeight() >= $sourceSize->getHeight()) {
            return $sourceSize;
        }

        if ($targetSize->getWidth() >= $sourceSize->getWidth()) {
            return $sourceSize;
        }

        return $targetSize;
    }
}
