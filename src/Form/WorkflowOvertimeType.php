<?php

namespace App\Form;

use App\Entity\WorkflowOvertime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowOvertimeType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('workedAt', DateType::class, [
				'label' => 'Date',
				'widget' => 'single_text',
				'input' => 'datetime_immutable',
			])
			->add('hourCount', NumberType::class, [
				'label' => 'Nombre d\'heures',
				'scale' => 2,
			])
			->add('unitHourPrice', NumberType::class, [
				'label' => 'Prix unitaire / heure',
				'scale' => 2,
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => WorkflowOvertime::class,
		]);
	}
}
