<?php

namespace App\Controller;

use App\DTO\ContractCompanyPart;
use App\Entity\Artist;
use App\Entity\ArtistItem;
use App\Entity\Contract;
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
        $contratCompanyPart = new ContractCompanyPart();
        $DTOService->transferDataTo($lastContract, $contratCompanyPart);
        $completedContract = $contractRepository->getUserLastCompletedContract($user);
        if($completedContract !== null) {
            $DTOService->transferDataTo($completedContract, $contratCompanyPart);
        }
        /** @var Show $relatedProject */
        $relatedProject = $lastContract->getRelatedProject();
        $contratCompanyPart->showName = $relatedProject->getName();
        $contratCompanyPart->showAuthors = $relatedProject->getAuthors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contratCompanyPart->showDirectors = $relatedProject->getDirectors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();
        $contratCompanyPart->showArtists = $relatedProject->getActors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray();

        $form = $this->createForm(ContractCompanyPartType::class, $contratCompanyPart);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $DTOService->transferDataTo($contratCompanyPart, $lastContract);
            $relatedProject->setName($contratCompanyPart->showName);
            foreach ($relatedProject->getActors() as $actorItem) {
                $entityManager->remove($actorItem);
            }
            $relatedProject->getActors()->clear();
            $i = 0;
            foreach ($contratCompanyPart->showArtists as $artist) {
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
            foreach ($contratCompanyPart->showAuthors as $artist) {
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
            foreach ($contratCompanyPart->showDirectors as $artist) {
                $artistItem = new ArtistItem();
                $artistItem->setArtist($artist);
                $artistItem->setDirectedProject($relatedProject);
                $artistItem->setPosition($i++);
                $entityManager->persist($artistItem);
                $relatedProject->getDirectors()->add($artistItem);
            }
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
