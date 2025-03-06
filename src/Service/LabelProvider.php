<?php

namespace App\Service;

use App\Entity\Contract;

class LabelProvider
{
    const LABEL_DICTIONARY = [
        Contract::TYPE_CO_PRODUCTION => 'Contrat de co-réalisation avec minimum garanti',
        Contract::TYPE_RENT_WITHOUT_STAGE_MANAGER => 'Contrat de location sans régisseur',
        Contract::TYPE_RENT_WITH_STAGE_MANAGER => 'Contrat de location avec régisseur',
        Contract::TYPE_CO_PRODUCTION_WITHOUT_MINIMUM => 'Contrat de co-réalisation sans minimum garanti'
    ];

    public static function provideLabelFor($key)
    {
        if (array_key_exists($key, self::LABEL_DICTIONARY)) {
            return self::LABEL_DICTIONARY[$key];
        }
        return '';
    }
}
