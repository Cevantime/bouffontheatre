<?php

namespace App\Controller;

use App\DTO\ContractCompanyPart;
use App\Entity\ArtistItem;
use App\Entity\Contract;
use App\Entity\Media;
use App\Entity\MediaGalleryItem;
use App\Entity\Project;
use App\Entity\Show;
use App\Entity\User;
use App\Form\ContractCompanyPartType;
use App\Repository\ContractRepository;
use App\Service\DTOService;
use App\Service\EmailService;
use App\Service\ImageCropService;
use App\Service\ProjectMediaService;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Extension\CoreExtension;

class ContractInformationsController extends AbstractController
{
    #[Route('/contract/informations', name: 'app_contract_informations')]
    #[IsGranted('ROLE_ARTIST')]
    public function form(
        DTOService $DTOService,
        ContractRepository $contractRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        EmailService $emailService,
        Pool $pool,
        ProjectMediaService $projectMediaService,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $contracts = $contractRepository->getUserContractsToComplete($user);
        if (! $contracts) {
            return $this->render('front/contract_informations/no_contract.html.twig');
        }
        set_time_limit(300);
        /** @var Contract $lastContract */
        $lastContract = $contracts[0];
        $contractCompanyPart = new ContractCompanyPart();
        $DTOService->transferDataTo($lastContract, $contractCompanyPart);
        $lastCompletedContract = $contractRepository->getUserLastCompletedContract($user);
        if ($lastCompletedContract !== null) {
            $DTOService->transferDataTo($lastCompletedContract, $contractCompanyPart);
        }
        /** @var Show $relatedProject */
        $relatedProject = $lastContract->getRelatedProject();
        $contractCompanyPart->showName = $relatedProject->getName();
        $contractCompanyPart->showAuthors = $relatedProject->getAuthors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contractCompanyPart->showDirectors = $relatedProject->getDirectors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contractCompanyPart->showArtists = $relatedProject->getActors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contractCompanyPart->showPunchline = $relatedProject->getPunchline();
        $contractCompanyPart->showDescription = $relatedProject->getDescription();
        $contractCompanyPart->showHasBanner = $relatedProject->getBanner() !== null;
        $contractCompanyPart->showHasPoster = $relatedProject->getPoster() !== null;

        // Générer les URLs publiques pour les médias existants
        if ($relatedProject->getBanner()) {
            $provider = $pool->getProvider($relatedProject->getBanner()->getProviderName());
            $contractCompanyPart->showBannerUrl = $provider->generatePublicUrl(
                $relatedProject->getBanner(),
                $provider->getFormatName($relatedProject->getBanner(), 'reference')
            );
        }
        if ($relatedProject->getPoster()) {
            $provider = $pool->getProvider($relatedProject->getPoster()->getProviderName());
            $contractCompanyPart->showPosterUrl = $provider->generatePublicUrl(
                $relatedProject->getPoster(),
                $provider->getFormatName($relatedProject->getPoster(), 'reference')
            );
        }

        $form = $this->createForm(ContractCompanyPartType::class, $contractCompanyPart);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $DTOService->transferDataTo($contractCompanyPart, $lastContract);
            $lastContract->setShowArtistCount(count($contractCompanyPart->showArtists));
            $lastContract->setShowDirector(CoreExtension::join($contractCompanyPart->showDirectors, ', ', ' et '));
            $lastContract->setShowAuthor(CoreExtension::join($contractCompanyPart->showAuthors, ', ', ' et '));
            $relatedProject->setPunchline($contractCompanyPart->showPunchline);
            $relatedProject->setDescription($contractCompanyPart->showDescription);
            $relatedProject->setName($contractCompanyPart->showName);
            foreach ($relatedProject->getActors() as $actorItem) {
                $entityManager->remove($actorItem);
            }
            $relatedProject->getActors()->clear();
            $i = 0;
            foreach ($contractCompanyPart->showArtists as $artist) {
                $artistItem = new ArtistItem();
                $artistItem->setArtist($artist);
                $artistItem->setActedProject($relatedProject);
                $artistItem->setPosition($i++);
                $entityManager->persist($artistItem);
                $relatedProject->getActors()->add($artistItem);
            }
            foreach ($relatedProject->getAuthors() as $authorItem) {
                $entityManager->remove($authorItem);
            }
            $relatedProject->getAuthors()->clear();
            $i = 0;
            foreach ($contractCompanyPart->showAuthors as $artist) {
                $artistItem = new ArtistItem();
                $artistItem->setArtist($artist);
                $artistItem->setAuthoredShow($relatedProject);
                $artistItem->setPosition($i++);
                $entityManager->persist($artistItem);
                $relatedProject->getAuthors()->add($artistItem);
            }
            foreach ($relatedProject->getDirectors() as $directorItem) {
                $entityManager->remove($directorItem);
            }
            $relatedProject->getDirectors()->clear();
            $i = 0;
            foreach ($contractCompanyPart->showDirectors as $artist) {
                $artistItem = new ArtistItem();
                $artistItem->setArtist($artist);
                $artistItem->setDirectedProject($relatedProject);
                $artistItem->setPosition($i++);
                $entityManager->persist($artistItem);
                $relatedProject->getDirectors()->add($artistItem);
            }

            /** @var File $file */
            $gallery = $relatedProject->getGallery();
            $i = $gallery->getGalleryItems()->count();

            // Traiter la collection de médias
            if ($contractCompanyPart->showMedia) {
                foreach ($contractCompanyPart->showMedia as $mediaData) {
                    // Créer le média via le service
                    $media = $projectMediaService->createMediaFromFormData(
                        $mediaData,
                        null,
                        'Photo n°' . ($i + 1)
                    );

                    if (!$media) {
                        continue;
                    }

                    // Ajouter à la galerie
                    $galleryItem = new MediaGalleryItem();
                    $galleryItem->setPosition($i++);
                    $galleryItem->setMedia($media);
                    $gallery->addGalleryItem($galleryItem);
                    $entityManager->persist($galleryItem);
                }
            }

            // Traiter showBanner
            $projectMediaService->updateProjectMedia(
                $relatedProject,
                is_array($contractCompanyPart->showBanner) ? $contractCompanyPart->showBanner : null,
                $contractCompanyPart->showBanner instanceof File ? $contractCompanyPart->showBanner : null,
                'banner',
                'Bannière de ' . $relatedProject->getName()
            );

            // Traiter showPoster
            $projectMediaService->updateProjectMedia(
                $relatedProject,
                is_array($contractCompanyPart->showPoster) ? $contractCompanyPart->showPoster : null,
                $contractCompanyPart->showPoster instanceof File ? $contractCompanyPart->showPoster : null,
                'poster',
                'Affiche de ' . $relatedProject->getName()
            );

            $emailService->sendMailTo($relatedProject->getOwner()->getEmail(), 'Fiche infos remplie', 'emails/contract_informations_filled_user.html.twig', [
                'contract' => $lastContract
            ]);
            $emailService->sendEmailToAdmins('Un formulaire d\'information contractuel a été rempli', 'emails/contract_informations_filled.html.twig', [
                'user' => $user,
                'contract' => $lastContract
            ]);
            $lastContract->setFetchDataStatus(Contract::FETCH_DATA_STATUS_FILLED_BY_COMPANY);
            $this->addFlash('success', 'Demande envoyée avec succès');
            $entityManager->persist($lastContract);
            $entityManager->flush();
            return $this->redirectToRoute('app_contract_informations');
        }

        return $this->render('front/contract_informations/form.html.twig', [
            'form' => $form->createView(),
            'contracts' => $contracts,
            'lastContract' => $lastContract
        ]);
    }
}
