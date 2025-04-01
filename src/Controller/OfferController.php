<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Media;
use App\Entity\Offer;
use App\Entity\User;
use App\Form\CandidatureType;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OfferController extends AbstractController
{
    #[Route(path: '/offer', name: 'app_offer')]
    public function index(OfferRepository$offerRepository): Response
    {
        $offers = $offerRepository->findAll();
        return $this->render('front/offer/index.html.twig', [
            'offers' => $offers
        ]);
    }

    #[Route(path: '/offer/{id}', name: 'app_offer_details')]
    public function details(Offer $offer, Request $request, EntityManagerInterface $entityManager, MediaManagerInterface $mediaManager): Response
    {
        $candidature = new Candidature();

        /** @var User $user */
        $user = $this->getUser();

        if($user) {
            $candidature->setEmail($user->getEmail());
            $candidature->setFullname($user->getFirstname().' '.$user->getLastname());
        }

        $form = $this->createForm(CandidatureType::class, $candidature);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $media = new Media();
            /** @var UploadedFile $cvFile */
            $cvFile = $form->get('cvFile')->getData();
            $media->setBinaryContent($cvFile->getRealPath());
            $media->setContext('default'); // video related to the user
            $media->setProviderName('sonata.media.provider.file');
            $candidature->setCv($media);
            $candidature->setOffer($offer);
            $mediaManager->save($media);
            $entityManager->persist($candidature);
            $entityManager->flush();
            $this->addFlash('success', 'Votre candidature a bien été envoyée');
            return $this->redirectToRoute('app_offer_details', ['id' => $offer->getId()]);
        }

        return $this->render('front/offer/details.html.twig', [
            'offer' => $offer,
            'form' => $form->createView()
        ]);
    }
}
