<?php

namespace App\Controller\Admin;

use App\Contract\ContractGenerator;
use App\DTO\ContractAdminPart;
use App\DTO\ContractCompanyPartAdmin;
use App\DTO\ContractGlobalConfig;
use App\Entity\Contract;
use App\Entity\Project;
use App\Form\ContractAdminPartType;
use App\Form\ContractGlobalConfigType;
use App\Repository\ContractRepository;
use App\Service\ConfigService;
use App\Service\DTOService;
use App\Service\EmailService;
use App\Service\StringCallbacks;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/app')]
class ContractController extends AbstractController
{

    #[Route(path: '/contract/config', name: 'app_contract_config')]
    public function form(
        Request       $request,
        ConfigService $configService,
        DTOService    $DTOService
    ): Response
    {
        $contractConfig = new ContractGlobalConfig();
        $configs = $configService->getRawConfigs();
        $DTOService->transferDataTo($configs, $contractConfig, StringCallbacks::class . '::camelize');
        $form = $this->createForm(ContractGlobalConfigType::class, $contractConfig);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $DTOService->transferDataTo($contractConfig, $configs, StringCallbacks::class . '::snakify');
            $configService->saveConfigs($configs);
        }
        return $this->render('sonata/contract/config.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/contract/from-project/{id}', name: 'app_contract_create_from_project', options: ['expose' => true])]
    #[Route(path: '/contract/from-project/{id}/edit/{idContract}', name: 'app_contract_edit_from_project', options: ['expose' => true])]
    public function contractForProjectForm(
        ConfigService          $configService,
        DTOService             $DTOService,
        Request                $request,
        EntityManagerInterface $entityManager,
        ContractRepository     $contractRepository,
        ContractGenerator      $contractService,
        ?Project               $project = null,
                               $idContract = null
    )
    {
        $contractAdminPart = new ContractAdminPart();
        $contractCompanyPart = new ContractCompanyPartAdmin();
        $contractConfig = new ContractGlobalConfig();
        if ($idContract) {
            $contract = $contractRepository->find($idContract);
            if (!$contract) {
                throw $this->createNotFoundException();
            }
            foreach ([$contractAdminPart, $contractConfig, $contractCompanyPart] as $form) {
                $DTOService->transferDataTo($contract, $form);
            }
        } else {
            $contract = new Contract();
        }

        $contractAdminPart->project = $project;
        $contractAdminPart->contractTheaterConfig = $contractConfig;
        $contractAdminPart->contractCompanyPart = $contractCompanyPart;
        $lastContract = $contractRepository->getUserLastCompletedContract($project->getOwner());
        if ($lastContract && !$idContract) {
            $DTOService->transferDataTo($lastContract, $contractCompanyPart);
            $DTOService->transferDataTo($lastContract, $contractConfig);
        } elseif (!$idContract) {
            $configs = $configService->getRawConfigs();
            $DTOService->transferDataTo($configs, $contractConfig, StringCallbacks::class . '::camelize');
        }
        $form = $this->createForm(ContractAdminPartType::class, $contractAdminPart);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contractDate = new \DateTimeImmutable();
            $contract->setContractDate($contractDate);
            $contractSignatureDate = $contractDate->add(\DateInterval::createFromDateString('14 day'));
            $contract->setContractSignatureDate($contractSignatureDate);
            foreach ($contractAdminPart->getPerformances() as $performance) {
                $performance->setRelatedProject($contractAdminPart->project);
                $performance->setContract($contract);
                $entityManager->persist($performance);
            }
            $contract->setRelatedProject($contractAdminPart->project);
            foreach ([$contractAdminPart, $contractConfig, $contractCompanyPart] as $form) {
                $DTOService->transferDataTo($form, $contract);
            }
            $entityManager->persist($contract);
            $entityManager->flush();
            if ($request->request->get('invite')) {
                return $this->redirectToRoute('app_contract_invite_company', ['id' => $contract->getId()]);
            } elseif ($request->request->get('generate')) {
                return $contractService->createGeneratedContractResponse($contract);
            } elseif ($request->request->get('send')) {
                return $this->redirectToRoute('app_contract_send_email', ['id' => $contract->getId()]);
            } elseif ($request->request->get('sign')) {
                return $this->redirectToRoute('app_contract_sign', ['id' => $contract->getId()]);
            } else {
                return $this->redirectToRoute('app_contract_edit_from_project', [
                    'id' => $contract->getRelatedProject()->getId(),
                    'idContract' => $contract->getId()
                ]);
            }
        }
        return $this->render('sonata/contract/theater_part.html.twig', [
            'form' => $form->createView(),
            'edit' => intval($idContract),
            'idContract' => $idContract
        ]);
    }

    #[Route(path: '/contract/invite-company/{id}', name: 'app_contract_invite_company')]
    public function inviteCompany(
        Contract               $contract,
        EntityManagerInterface $entityManager,
        EmailService           $emailService,
    )
    {
        $contract->setFetchDataStatus(Contract::FETCH_DATA_STATUS_SENT_TO_COMPANY);
        $entityManager->persist($contract);
        $entityManager->flush();
        $emailService->sendFetchDataFormMail($contract);
        $this->addFlash('success', 'Formulaire envoyé avec succès');
        return $this->redirectToRoute('admin_app_contract_show', ['id' => $contract->getId()]);
    }

    #[Route('/contract/generate/{id}', name: 'app_contract_generate')]
    public function generateContract(Contract $contract, ContractGenerator $contractService)
    {
        return $contractService->createGeneratedContractResponse($contract);
    }

    #[Route('/contract/sign/{id}', name: 'app_contract_sign')]
    public function signContract(Contract $contract, EntityManagerInterface $entityManager)
    {
        $contract->setStatus(Contract::STATUS_SIGNED);
        $entityManager->persist($contract);
        $entityManager->flush();
        return $this->redirectToRoute('admin_app_contract_show', ['id' => $contract->getId()]);
    }

    #[Route('/contract/send-email/{id}', name: 'app_contract_send_email')]
    public function sendByEmail(Contract $contract, EmailService $emailService, EntityManagerInterface $entityManager)
    {
        $contract->setStatus(Contract::STATUS_SENT_TO_COMPANY);
        $entityManager->persist($contract);
        $entityManager->flush();
        $emailService->sendContractMail($contract);
        $this->addFlash('success', 'Contrat envoyé avec succès');
        return $this->redirectToRoute('admin_app_contract_show', ['id' => $contract->getId()]);
    }
}
