<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TempImageUploadController extends AbstractController
{
	#[Route('/temp-image-upload', name: 'app_temp_image_upload', methods: ['POST'])]
	public function upload(Request $request): JsonResponse
	{
		/** @var UploadedFile $file */
		$file = $request->files->get('file');

		if (!$file) {
			return new JsonResponse(['error' => 'No file uploaded'], 400);
		}

		// Créer un répertoire temporaire dans le dossier public
		$tempDir = $this->getParameter('kernel.project_dir') . '/public/temp-uploads';
		if (!is_dir($tempDir)) {
			mkdir($tempDir, 0777, true);
		}

		// Générer un nom unique
		$filename = uniqid() . '.' . $file->guessExtension();
		$file->move($tempDir, $filename);

		// Retourner l'URL publique temporaire
		return new JsonResponse([
			'success' => true,
			'tempPath' => '/temp-uploads/' . $filename,
			'filename' => $filename,
		]);
	}

	#[Route('/temp-image-crop', name: 'app_temp_image_crop', methods: ['POST'])]
	public function crop(Request $request): JsonResponse
	{
		$data = json_decode($request->getContent(), true);

		$tempPath = $data['tempPath'] ?? null;
		$cropData = $data['cropData'] ?? null;

		if (!$tempPath || !$cropData) {
			return new JsonResponse(['error' => 'Missing data'], 400);
		}

		// Stocker les données de crop pour ce fichier temporaire
		// On pourrait les sauvegarder en session ou dans un fichier JSON
		$session = $request->getSession();
		$cropDataStore = $session->get('crop_data', []);
		$cropDataStore[$tempPath] = $cropData;
		$session->set('crop_data', $cropDataStore);

		return new JsonResponse(['success' => true]);
	}
}
