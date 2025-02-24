<?php

namespace App\Controller;

use App\DTO\ContractCompanyPart;
use App\Entity\Artist;
use App\Entity\ArtistItem;
use App\Entity\Contract;
use App\Entity\Media;
use App\Entity\MediaGalleryItem;
use App\Entity\Show;
use App\Entity\User;
use App\Form\ContractCompanyPartType;
use App\Repository\ContractRepository;
use App\Service\ConfigService;
use App\Service\DTOService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContractInformationsController extends AbstractController
{
    #[Route('/contract/informations', name: 'app_contract_informations')]
    #[IsGranted('ROLE_ARTIST')]
    public function form(
        DTOService $DTOService,
        ContractRepository $contractRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $contracts = $contractRepository->getUserContractsToComplete($user);
        if( ! $contracts) {
            return $this->render('front/contract_informations/no_contract.html.twig');
        }
        /** @var Contract $lastContract */
        $lastContract = $contracts[0];
        $contractCompanyPart = new ContractCompanyPart();
        $DTOService->transferDataTo($lastContract, $contractCompanyPart);
        $completedContract = $contractRepository->getUserLastCompletedContract($user);
        if($completedContract !== null) {
            $DTOService->transferDataTo($completedContract, $contractCompanyPart);
        }
        /** @var Show $relatedProject */
        $relatedProject = $lastContract->getRelatedProject();
        $contractCompanyPart->showName = $relatedProject->getName();
        $contractCompanyPart->showAuthors = $relatedProject->getAuthors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contractCompanyPart->showDirectors = $relatedProject->getDirectors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contractCompanyPart->showArtists = $relatedProject->getActors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contractCompanyPart->showPunchline = $relatedProject->getPunchline();
        $contractCompanyPart->showDescription = $relatedProject->getDescription();
        $form = $this->createForm(ContractCompanyPartType::class, $contractCompanyPart);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $DTOService->transferDataTo($contractCompanyPart, $lastContract);
            $lastContract->setShowArtistCount(count($contractCompanyPart->showArtists));
            $lastContract->setShowDirector($this->twig_join_filter($contractCompanyPart->showDirectors, ', ', ' et '));
            $lastContract->setShowAuthor($this->twig_join_filter($contractCompanyPart->showAuthors, ', ', ' et '));
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
            foreach ($contractCompanyPart->showMedia as $file) {
                $media = new Media();
                $media->setProviderName('sonata.media.provider.image');
                $media->setContext('default');
                $media->setName('Element n°'.$i);
                $media->setBinaryContent($file->getRealPath());
                $galleryItem = new MediaGalleryItem();
                $galleryItem->setPosition($i++);
                $galleryItem->setMedia($media);
                $gallery->addGalleryItem($galleryItem);
                $entityManager->persist($media);
                $entityManager->persist($galleryItem);
            }

            $bannerMedia = new Media();
            $bannerMedia->setProviderName('sonata.media.provider.image');
            $bannerMedia->setName('Bannière de '.$relatedProject->getName());
            $bannerMedia->setContext('default');
            $bannerMedia->setBinaryContent($contractCompanyPart->showBanner->getRealPath());

            $entityManager->persist($bannerMedia);

            $relatedProject->setBanner($bannerMedia);

            $posterMedia = new Media();
            $posterMedia->setProviderName('sonata.media.provider.image');
            $posterMedia->setName('Affiche de '.$relatedProject->getName());
            $posterMedia->setContext('default');
            $posterMedia->setBinaryContent($contractCompanyPart->showPoster->getRealPath());

            $entityManager->persist($posterMedia);

            $relatedProject->setPoster($posterMedia);

            $emailService->sendMailTo($relatedProject->getOwner()->getEmail(), 'Fiche infos remplie', 'emails/contract_informations_filled_user.html.twig', [
                'contract' => $lastContract
            ]);
            $emailService->sendEmailToAdmins('Un formulaire d\'information contractuel a été rempli', 'emails/contract_informations_filled.html.twig', [
                'user' => $user,
                'contract' => $lastContract
            ]);
            $lastContract->setStatus(Contract::STATUS_FILLED_BY_COMPANY);
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

    private function twig_join_filter($value, $glue = '', $and = null)
    {
        if (!($value instanceof \Traversable || \is_array($value))) {
            $value = (array) $value;
        }

        $value = $this->twig_to_array($value, false);

        if (0 === \count($value)) {
            return '';
        }

        if (null === $and || $and === $glue) {
            return implode($glue, $value);
        }

        if (1 === \count($value)) {
            return $value[0];
        }

        return implode($glue, \array_slice($value, 0, -1)).$and.$value[\count($value) - 1];
    }

    function twig_to_array($seq, $preserveKeys = true)
    {
        if ($seq instanceof \Traversable) {
            return iterator_to_array($seq, $preserveKeys);
        }

        if (!\is_array($seq)) {
            return $seq;
        }

        return $preserveKeys ? $seq : array_values($seq);
    }

}
