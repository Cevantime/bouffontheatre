<?php

namespace App\DTO;

use App\Entity\Performance;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class WorkflowRevenue
{

    #[Assert\File(
        maxSize: '4M',
        mimeTypes: ['application/pdf'],
        mimeTypesMessage: 'Veuillez uploader un fichier PDF valide.'
    )]
    public ?UploadedFile $revenueTickBossFile;
    public ?bool $copyrightApplicable = true;
    public ?bool $retirementContribApplicable = true;

    public ?bool $agessaContribApplicable = true;

    public array|Collection $performances;
}
