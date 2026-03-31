<?php

namespace App\Service;

use App\Entity\Media;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ZipArchive;

class MediaZipService
{
	public function __construct(
		private Pool $pool,
		#[Autowire('%kernel.project_dir%')]
		private string $projectDir,
	) {}

	public function addMediaToZip(?Media $media, string $zipPathWithoutExt, ZipArchive $zip): void
	{
		if (!$media) {
			return;
		}

		$provider = $this->pool->getProvider($media->getProviderName());
		$publicUrl = $provider->generatePublicUrl($media, $provider->getFormatName($media, 'reference'));
		$publicPath = parse_url($publicUrl, PHP_URL_PATH);

		if (!$publicPath) {
			return;
		}

		$absolutePath = $this->projectDir . '/public' . $publicPath;
		if (!is_file($absolutePath)) {
			return;
		}

		$extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
		$zipPath = $zipPathWithoutExt;
		if ($extension !== '' && pathinfo($zipPathWithoutExt, PATHINFO_EXTENSION) === '') {
			$zipPath .= '.' . $extension;
		}

		$zip->addFile($absolutePath, $zipPath);
	}
}
