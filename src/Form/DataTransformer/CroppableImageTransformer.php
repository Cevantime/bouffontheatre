<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;

class CroppableImageTransformer implements DataTransformerInterface
{
	/**
	 * Transforme l'objet File en tableau pour le formulaire
	 */
	public function transform(mixed $value): mixed
	{
		if (null === $value) {
			return [
				'file' => null,
				'cropData' => null,
				'tempPath' => null,
			];
		}

		return [
			'file' => $value,
			'cropData' => null,
			'tempPath' => null,
		];
	}

	/**
	 * Transforme le tableau du formulaire en File
	 */
	public function reverseTransform(mixed $value): mixed
	{
		if (!is_array($value)) {
			return null;
		}

		// Toujours retourner le tableau, même s'il n'y a pas de nouveau fichier
		// (peut contenir juste des données de crop)
		return $value;
	}
}
