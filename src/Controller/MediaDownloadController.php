<?php

namespace App\Controller;

use App\Entity\Download;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\DownloadRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaDownloadController extends AbstractController
{
    #[IsGranted('ROLE_PRO')]
    #[Route(path: '/app/media/download/{id}', name: 'app_media_download')]
    public function download(Media $media, DownloadRepository $downloadRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $download = $downloadRepository->findOneBy(['media' => $media, 'user' => $user ]);
        if($download === null) {
            $download = new Download();
            $download->setMedia($media);
            $download->setUser($user);
            $downloadRepository->add($download, true);
        }
        return $this->redirectToRoute('sonata_media_download', ['id' => $media->getId()]);
    }
}
