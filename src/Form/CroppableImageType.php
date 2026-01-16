<?php

namespace App\Form;

use App\Form\DataTransformer\CroppableImageTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CroppableImageType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('file', FileType::class, [
				'label' => false,
				'required' => false,
				'attr' => [
					'accept' => 'image/jpeg,image/png',
					'data-croppable-target' => 'fileInput',
				],
				'constraints' => [
					new Assert\Image([
						'mimeTypes' => ['image/jpeg', 'image/png'],
						'mimeTypesMessage' => 'Veuillez fournir une image valide (formats acceptés : JPG, PNG).',
						'maxSize' => '4M',
						'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 4 Mo.',
					]),
				],
			])
			->add('cropData', HiddenType::class, [
				'label' => false,
				'required' => false,
				'attr' => [
					'data-croppable-target' => 'cropData',
				],
			])
			->add('tempPath', HiddenType::class, [
				'label' => false,
				'required' => false,
				'attr' => [
					'data-croppable-target' => 'tempPath',
				],
			]);

		// Ajouter le DataTransformer pour convertir le tableau
		$builder->addModelTransformer(new CroppableImageTransformer());
	}

	public function buildView(FormView $view, FormInterface $form, array $options): void
	{
		$view->vars['aspect_ratio'] = $options['aspect_ratio'];
		$view->vars['existing_url'] = $options['existing_url'];
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'aspect_ratio' => 16 / 9,
			'existing_url' => null,
			'label' => false,
		]);
	}

	public function getBlockPrefix(): string
	{
		return 'croppable_image';
	}
}
