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
        $contratCompanyPart->showAuthor = twig_join_filter($relatedProject->getAuthors()->count() ? $relatedProject->getAuthors()->map(fn(ArtistItem $a) => $a->getArtist()->getFullname())->toArray() : '', ', ', ' et ');
        $contratCompanyPart->showDirector = twig_join_filter($relatedProject->getDirectors()->count() ? $relatedProject->getDirectors()->map(fn(ArtistItem $a) => $a->getArtist()->getFullname())->toArray() : '', ', ', ' et ');

        $form = $this->createForm(ContractCompanyPartType::class, $contratCompanyPart);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $DTOService->transferDataTo($contratCompanyPart, $lastContract);
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
}
