<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ArtistAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Artist::class,
            'placeholder' => 'Choose a Artist',
            'choice_label' => 'fullName',
            'label' => 'Artistes présents en scène',
            'multiple' => true,
            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['firstname', 'lastname'],
            'attr' => [
                'data-controller' => 'contract-artist-autocomplete',
            ],
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
