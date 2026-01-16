<?php

namespace App\Service;

use App\Entity\Media;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Sonata\MediaBundle\Provider\Pool;

class ProjectMediaService
{
	public function __construct(
		private ImageCropService $imageCropService,
		private Pool $pool,
		private EntityManagerInterface $entityManager,
		#[Autowire(service: 'sonata.media.manager.media')]
		private object $mediaManager,
		#[Autowire('%kernel.project_dir%')]
		private string $projectDir,
	) {}

	/**
	 * Met à jour un média du projet (banner/poster) avec support upload + crop
	 */
	public function updateProjectMedia(
		Project $project,
		?array $mediaData,
		?File $fallbackFile,
		string $mediaField,
		string $mediaName
	): void {
		// Gestion du fallback pour compatibilité avec ancien code
		if ($fallbackFile instanceof File) {
			$this->saveNewMedia($project, $mediaField, $fallbackFile, $mediaName);
			return;
		}

		// Pas de données à traiter
		if (!is_array($mediaData)) {
			return;
		}

		$file = $mediaData['file'] ?? null;
		$cropData = isset($mediaData['cropData']) && !empty($mediaData['cropData'])
			? json_decode($mediaData['cropData'], true)
			: null;

		// Si on a un nouveau fichier OU des données de crop
		if (!$file instanceof File && !$cropData) {
			return;
		}

		// Appliquer le crop si nécessaire
		if ($file instanceof File) {
			// Nouveau fichier : appliquer le crop si présent
			if ($cropData && isset($mediaData['tempPath'])) {
				$file = $this->applyCropToTempFile($mediaData['tempPath'], $cropData);
			}
		} else {
			// Pas de nouveau fichier, mais données de crop : appliquer le crop à l'image existante
			if ($cropData) {
				$file = $this->cropExistingMedia($project, $mediaField, $cropData);
			}
		}

		if (!$file) {
			return;
		}

		// Supprimer l'ancien media et sauvegarder le nouveau
		$this->deleteOldMedia($project, $mediaField);
		$this->saveNewMedia($project, $mediaField, $file, $mediaName);
	}

	/**
	 * Crée un média depuis les données du formulaire (pour galerie, etc.)
	 * Retourne le Media créé
	 */
	public function createMediaFromFormData(
		?array $mediaData,
		?File $fallbackFile,
		string $mediaName
	): ?Media {
		// Gestion du fallback pour compatibilité avec ancien code
		if ($fallbackFile instanceof File) {
			return $this->createNewMedia($fallbackFile, $mediaName);
		}

		// Pas de données à traiter
		if (!is_array($mediaData)) {
			return null;
		}

		$file = $mediaData['file'] ?? null;
		$cropData = isset($mediaData['cropData']) && !empty($mediaData['cropData'])
			? json_decode($mediaData['cropData'], true)
			: null;

		// Si on a un nouveau fichier OU des données de crop
		if (!$file instanceof File && !$cropData) {
			return null;
		}

		// Appliquer le crop si nécessaire
		if ($file instanceof File) {
			// Nouveau fichier : appliquer le crop si présent
			if ($cropData && isset($mediaData['tempPath'])) {
				$file = $this->applyCropToTempFile($mediaData['tempPath'], $cropData);
			}
		} else {
			// Pas de nouveau fichier seul, mais données de crop nécessitent un nouveau fichier
			return null;
		}

		if (!$file) {
			return null;
		}

		return $this->createNewMedia($file, $mediaName);
	}

	/**
	 * Applique le crop à un fichier temporaire
	 */
	private function applyCropToTempFile(string $tempPath, array $cropData): ?File
	{
		$fullPath = $this->projectDir . '/public' . $tempPath;

		if (!file_exists($fullPath)) {
			return null;
		}

		$croppedPath = $this->imageCropService->applyCrop($fullPath, $cropData);
		return new File($croppedPath);
	}

	/**
	 * Applique le crop à un média existant
	 */
	private function cropExistingMedia(
		Project $project,
		string $mediaField,
		array $cropData
	): ?File {
		$getter = 'get' . ucfirst($mediaField);
		$media = $project->$getter();

		if (!$media) {
			return null;
		}

		$provider = $this->pool->getProvider($media->getProviderName());
		$existingPath = $this->projectDir . '/public' . $provider->generatePublicUrl(
			$media,
			$provider->getFormatName($media, 'reference')
		);

		if (!file_exists($existingPath)) {
			return null;
		}

		$croppedPath = $this->imageCropService->applyCrop($existingPath, $cropData);
		return new File($croppedPath);
	}

	/**
	 * Supprime l'ancien média associé
	 */
	private function deleteOldMedia(Project $project, string $mediaField): void
	{
		$getter = 'get' . ucfirst($mediaField);
		$oldMedia = $project->$getter();

		if (!$oldMedia) {
			return;
		}

		$setter = 'set' . ucfirst($mediaField);
		$project->$setter(null);
		$this->entityManager->flush();
		$this->entityManager->remove($oldMedia);
		$this->mediaManager->delete($oldMedia);
	}

	/**
	 * Crée et sauvegarde un nouveau média
	 */
	private function saveNewMedia(
		Project $project,
		string $mediaField,
		File $file,
		string $mediaName
	): void {
		$media = $this->createNewMedia($file, $mediaName);

		$setter = 'set' . ucfirst($mediaField);
		$project->$setter($media);
	}

	/**
	 * Crée un objet Media et le persiste
	 */
	private function createNewMedia(File $file, string $mediaName): Media
	{
		$media = new Media();
		$media->setProviderName('sonata.media.provider.image');
		$media->setName($mediaName);
		$media->setContext('default');
		$media->setBinaryContent($file);

		$this->entityManager->persist($media);
		$this->mediaManager->save($media);

		return $media;
	}
}
