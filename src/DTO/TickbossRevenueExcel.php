<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class TickbossRevenueExcel
{
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        mimeTypesMessage: 'Veuillez uploader un fichier .xlsx valide.'
    )]
    public UploadedFile $revenueExcel;
}
