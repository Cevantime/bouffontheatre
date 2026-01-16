<?php

namespace App\Service;

use Intervention\Image\ImageManager;

class ImageCropService
{
	public function __construct(
		private ImageManager $imageManager,
		private string $projectDir
	) {}

	/**
	 * Applique les données de crop à une image et retourne le chemin du fichier croppé
	 */
	public function applyCrop(string $sourcePath, array $cropData): string
	{
		$image = $this->imageManager->make($sourcePath);

		// Appliquer le crop
		if (isset($cropData['width'], $cropData['height'], $cropData['x'], $cropData['y'])) {
			$image->crop(
				(int) round($cropData['width']),
				(int) round($cropData['height']),
				(int) round($cropData['x']),
				(int) round($cropData['y'])
			);
		}

		// Appliquer la rotation si présente
		if (isset($cropData['rotate']) && $cropData['rotate'] != 0) {
			$image->rotate(-$cropData['rotate']);
		}

		// Sauvegarder dans un fichier temporaire
		$tempDir = $this->projectDir . '/var/temp_cropped';
		if (!is_dir($tempDir)) {
			mkdir($tempDir, 0777, true);
		}

		$croppedPath = $tempDir . '/' . uniqid() . '.jpg';
		$image->save($croppedPath, 90);

		return $croppedPath;
	}

	/**
	 * Nettoie les fichiers temporaires
	 */
	public function cleanupTempFiles(): void
	{
		$tempDirs = [
			$this->projectDir . '/public/temp-uploads',
			$this->projectDir . '/var/temp_cropped',
		];

		foreach ($tempDirs as $dir) {
			if (is_dir($dir)) {
				$files = glob($dir . '/*');
				foreach ($files as $file) {
					if (is_file($file) && (time() - filemtime($file)) > 3600) { // Plus de 1h
						unlink($file);
					}
				}
			}
		}
	}
}
