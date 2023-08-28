<?php

namespace App\Form;

use App\Entity\Candidature;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email'
            ])
            ->add('fullname', null, [
                'label' => 'Votre nom'
            ])
            ->add('comment', CKEditorType::class, [
                'label' => 'Commentaire',
                'help' => 'Qui êtes-vous ? Qu\'est-ce qui vous motive ? Parlez-nous de votre candidature'
            ])
            ->add('cvFile', FileType::class, [
                'label' => 'Votre CV',
                'mapped' => false,
                'help' => 'Un CV à jour nous aide à mieux cerner votre candidature',
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Votre cv doit être au format pdf'
                    ])
                ]
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Vérification',
                'help' => 'Veuillez saisir les caractères affiché dans l\'image dans le champs ci-dessus'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer ma candidature',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}
