<?php

namespace App\Controller\Admin;

use _PHPStan_7d8742a37\Symfony\Component\Console\Exception\LogicException;
use App\Contract\ContractFactory;
use App\DTO\TickbossRevenueExcel;
use App\DTO\WorkflowRevenue;
use App\DTO\WorkflowSelectProject;
use App\DTO\WorkflowShopLink;
use App\Entity\Contract;
use App\Entity\Link;
use App\Entity\LinkItem;
use App\Entity\Media;
use App\Entity\Show;
use App\Entity\User;
use App\Entity\Workflow;
use App\Form\TickbossRevenueExcelType;
use App\Form\WorkflowContractType;
use App\Form\WorkflowRevenueType;
use App\Form\WorkflowSelectProjectFormType;
use App\Form\WorkflowShopLinkType;
use App\Form\WorkflowUserType;
use App\Repository\ContractRepository;
use App\Service\DTOService;
use App\Service\EmailService;
use App\Service\WorkflowService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/app')]
class WorkflowController extends AbstractController
{
    #[Route(path: '/workflow/create', name: 'app_workflow_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workflowSelectProject = new WorkflowSelectProject();

        $form = $this->createForm(WorkflowSelectProjectFormType::class, $workflowSelectProject);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workflow = new Workflow();
            $workflowSelectProject->show->setOwner($workflowSelectProject->owner);
            $workflow->setAssociatedShow($workflowSelectProject->show);
            $entityManager->persist($workflow);
            $entityManager->flush();
            return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
        }

        return $this->render('sonata/workflow/create.html.twig', [
            'owner_select_form' => $form->createView()
        ]);
    }

    #[Route(path: '/workflow/edit/{id}', name: 'app_workflow_edit', options: ['expose' => true])]
    public function edit(
        Workflow               $workflow,
        WorkflowService        $workflowService,
        Request                $request,
        EntityManagerInterface $entityManager,
        ContractFactory        $contractFactory,
        DTOService             $DTOService,
    ): Response {
        $viewData = [
            'workflow' => $workflow,
            'workflow_service' => $workflowService
        ];
        $contract = $workflow->getContract();
        if ($contract === null) {
            $contract = $contractFactory->createDefaultContract($workflow->getAssociatedShow());
        } else if ($contract->getRelatedProject() !== $workflow->getAssociatedShow()) {
            throw new LogicException("Project associated with a workflow is different from the contract's project");
        }
        if (!$workflowService->isWorkflowContractFrozen($workflow)) {
            $contractForm = $this->createForm(WorkflowContractType::class, $contract);
            $contractForm->handleRequest($request);
            if ($contractForm->isSubmitted() && $contractForm->isValid()) {
                $workflow->setContract($contract);
                foreach ($contract->getPerformances() as $performance) {
                    $performance->setRelatedProject($workflow->getAssociatedShow());
                    $performance->setContract($contract);
                    $entityManager->persist($performance);
                }
                $entityManager->persist($contract);
                $entityManager->flush();
            }
            $viewData['contractForm'] = $contractForm->createView();
        }
        $workflowShopLink = new WorkflowShopLink();
        $workflowShopLink->title = 'Billetreduc';
        $shopLinkForm = $this->createForm(WorkflowShopLinkType::class, $workflowShopLink);
        $shopLinkForm->handleRequest($request);
        if ($shopLinkForm->isSubmitted() && $shopLinkForm->isValid()) {
            $link = new Link();
            $linkItem = new LinkItem();
            $linkItem->setLink($link);
            $linkItem->setShopLinkedShow($workflow->getAssociatedShow());
            $linkItem->setPosition($workflow->getAssociatedShow()->getShopLinks()->count());
            $DTOService->transferDataTo($workflowShopLink, $link);
            $workflow->getAssociatedShow()->getShopLinks()->add($linkItem);
            $entityManager->persist($link);
            $entityManager->persist($linkItem);
            $entityManager->flush();
        }
        if ($workflowService->workflowCanAccess($workflow, Workflow::STEP_REVENUE_DECLARATION)) {
            $tickbossExcel = new TickbossRevenueExcel();

            $tickbossExcelForm = $this->createForm(TickbossRevenueExcelType::class, $tickbossExcel);

            $tickbossExcelForm->handleRequest($request);

            if ($tickbossExcelForm->isSubmitted() && $tickbossExcelForm->isValid()) {
                $workflowService->prefillPerformancesWithRevenueExcel($workflow, $tickbossExcel->revenueExcel);
            }

            $viewData['workflow_revenue_excel_form'] = $tickbossExcelForm->createView();

            $workflowRevenue = new WorkflowRevenue();
            $DTOService->transferDataTo($workflow, $workflowRevenue);
            $workflowRevenue->performances = $workflow->getContract()->getPerformances();
            $workflowRevenueForm = $this->createForm(WorkflowRevenueType::class, $workflowRevenue);
            $workflowRevenueForm->handleRequest($request);
            $viewData['workflow_revenue_form'] = $workflowRevenueForm;
            if ($workflowRevenueForm->isSubmitted() && $workflowRevenueForm->isValid()) {
                $DTOService->transferDataTo($workflowRevenue, $workflow);
                if ($request->files->get('workflow_revenue')['revenueTickBossFile'] !== null) {
                    $media = new Media();
                    $media->setProviderName('sonata.media.provider.file');
                    $media->setContext('default');
                    $media->setName('Déclaration des recettes Tickboss');
                    $media->setBinaryContent($workflowRevenue->revenueTickBossFile->getRealPath());
                    $workflow->setRevenueTickBossPdf($media);
                    $entityManager->persist($media);
                }
                $entityManager->flush();
            }
        }
        $viewData['shop_link_form'] = $shopLinkForm;
        return $this->render('sonata/workflow/edit.html.twig', $viewData);
    }

    #[Route(path: '/workflow/create-from-contract/{id}', name: 'app_workflow_create_from_contract', options: ['expose' => true])]
    public function createFromContract(Contract $contract, EntityManagerInterface $entityManager)
    {
        if ($contract->getWorkflow() !== null) {
            throw $this->createAccessDeniedException();
        }
        $workflow = new Workflow();
        $workflow->setContract($contract);
        $workflow->setAssociatedShow($contract->getRelatedProject());
        $entityManager->persist($workflow);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/send-contract/{id}', name: 'app_workflow_send_contract')]
    public function sendContract(Workflow $workflow, EmailService $emailService, EntityManagerInterface $entityManager)
    {
        $contract = $workflow->getContract();
        if ($contract == null) {
            throw $this->createAccessDeniedException();
        }
        $contractDate = new \DateTimeImmutable();
        $contract->setContractDate($contractDate);
        $contractSignatureDate = $contractDate->add(\DateInterval::createFromDateString('14 day'));
        $contract->setContractSignatureDate($contractSignatureDate);
        $contract->setStatus(Contract::STATUS_SENT_TO_COMPANY);
        $entityManager->persist($contract);
        $entityManager->flush();
        $emailService->sendContractMail($contract);
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/skip-send-contract/{id}', name: 'app_admin_workflow_skip_send_contract')]
    public function skipSendContract(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->getContract()->setStatus(Contract::STATUS_SENT_TO_COMPANY);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/send-data-form/{id}', name: 'app_workflow_send_data_form')]
    public function sendContractDataForm(Workflow $workflow, EmailService $emailService, EntityManagerInterface $entityManager)
    {
        $contract = $workflow->getContract();
        if ($contract == null) {
            throw $this->createAccessDeniedException();
        }
        $contract->setFetchDataStatus(Contract::FETCH_DATA_STATUS_SENT_TO_COMPANY);
        $entityManager->persist($contract);
        $entityManager->flush();
        $emailService->sendFetchDataFormMail($contract);
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/confirm-data-fetch/{id}', name: 'app_workflow_confirm_data_fetch')]
    public function confirmDataFetch(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->getContract()->setFetchDataStatus(Contract::FETCH_DATA_STATUS_FILLED_BY_COMPANY);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/sign-contract/{id}', name: 'app_workflow_sign_contract')]
    public function signContract(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $contract = $workflow->getContract();
        if ($contract == null) {
            throw $this->createNotFoundException();
        }
        $contract->setStatus(Contract::STATUS_SIGNED);
        $entityManager->persist($contract);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/highlight/{id}', name: 'app_workflow_highlight')]
    public function highlight(Workflow $workflow, WorkflowService $workflowService)
    {
        $workflowService->highlight($workflow);
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/skip-highlight/{id}', name: 'app_workflow_skip_highlight')]
    public function skipHighlight(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setShowHighlighted(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/remove/{id}', name: 'app_workflow_remove')]
    public function remove(Workflow $workflow, WorkflowService $workflowService)
    {
        $workflowService->remove($workflow);
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/skip-remove/{id}', name: 'app_workflow_skip_remove')]
    public function skipRemove(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setShowRemoved(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }


    #[Route(path: '/workflow/download-revenue-excel/{id}', name: 'app_workflow_download_excel_revenue')]
    public function downloadRevenueExcel(Workflow $workflow, WorkflowService $workflowService)
    {
        if (! $workflowService->workflowValidated($workflow, Workflow::STEP_REVENUE_DECLARATION)) {
            throw $this->createAccessDeniedException();
        }
        $revenueExport = $workflowService->generateRevenueExport($workflow);

        $binaryFileResponse = new BinaryFileResponse($revenueExport->path);

        $binaryFileResponse->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $revenueExport->name
        );

        return $binaryFileResponse;
    }


    #[Route(path: '/workflow/send-revenue-emails/{id}', name: 'app_workflow_send_revenue_emails')]
    public function sendRevenueEmails(Workflow $workflow, WorkflowService $workflowService, EmailService $emailService, EntityManagerInterface $entityManager)
    {
        if (! $workflowService->workflowValidated($workflow, Workflow::STEP_REVENUE_DECLARATION)) {
            throw $this->createAccessDeniedException();
        }
        $revenueReport = $workflowService->generateRevenueExport($workflow);
        if ($revenueReport->rawCompanyRevenue > 0.0) {
            $emailService->sendRevenueEmailToPresident($workflow, $revenueReport);
        }
        $emailService->sendRevenueEmailToCompany($workflow, $revenueReport);
        $workflow->setRevenueEmailSentToPresident(true);
        $workflow->setRevenueEmailSentToCompany(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/send-revenue-email-to-president/{id}', name: 'app_workflow_send_revenue_email_to_president')]
    public function sendRevenueEmailToPresident(Workflow $workflow, WorkflowService $workflowService, EmailService $emailService, EntityManagerInterface $entityManager)
    {
        if (! $workflowService->workflowValidated($workflow, Workflow::STEP_REVENUE_DECLARATION)) {
            throw $this->createAccessDeniedException();
        }
        $revenueReport = $workflowService->generateRevenueExport($workflow);
        if ($revenueReport->rawCompanyRevenue > 0.0) {
            $emailService->sendRevenueEmailToPresident($workflow, $revenueReport);
        }
        $workflow->setRevenueEmailSentToPresident(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }


    #[Route(path: '/workflow/send-revenue-email-to-company/{id}', name: 'app_workflow_send_revenue_email_to_company')]
    public function sendRevenueEmailToCompany(Workflow $workflow, WorkflowService $workflowService, EmailService $emailService, EntityManagerInterface $entityManager)
    {
        if (! $workflowService->workflowValidated($workflow, Workflow::STEP_REVENUE_DECLARATION)) {
            throw $this->createAccessDeniedException();
        }
        $revenueReport = $workflowService->generateRevenueExport($workflow);
        $emailService->sendRevenueEmailToCompany($workflow, $revenueReport);
        $workflow->setRevenueEmailSentToCompany(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }


    #[Route(path: '/workflow/skip-send-revenue-emails/{id}', name: 'app_workflow_skip_send_revenue_emails')]
    public function skipSendEmails(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setRevenueEmailSentToPresident(true);
        $workflow->setRevenueEmailSentToCompany(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/validate-sibil/{id}', name: 'app_workflow_validate_sibil')]
    public function validateSibil(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setSibilDone(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/validate-dectanet/{id}', name: 'app_workflow_validate_dectanet')]
    public function validateDectanet(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setDectanetDone(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/validate-manual-steps/{id}', name: 'app_workflow_validate_manual_steps')]
    public function validateManualSteps(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setManualStepsDone(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/close/{id}', name: 'app_admin_workflow_close')]
    public function closeWorkflow(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setClosed(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/open/{id}', name: 'app_admin_workflow_open')]
    public function openWorkflow(Workflow $workflow, EntityManagerInterface $entityManager)
    {
        $workflow->setClosed(false);
        $entityManager->flush();
        return $this->redirectToRoute('app_workflow_edit', ['id' => $workflow->getId()]);
    }

    #[Route(path: '/workflow/ajax/create-user', name: 'app_workflow_ajax_create_user', options: ['expose' => true])]
    public function createUserAjax(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->addRole(User::ROLE_ARTIST);
        $user->setNewsletter(true);
        $form = $this->createForm(WorkflowUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json([
                'status' => 'ok',
                'new_id' => $user->getId(),
                'message' => 'user created',
                'fullName' => $user->getFullname()
            ]);
        }
        $html = $this->renderView('sonata/workflow/ajax/create-user.html.twig', [
            'form' => $form->createView()
        ]);
        if ($request->isXmlHttpRequest()) {
            return $this->json(['status' => 'ko', 'html' => $html]);
        } else {
            return new Response($html);
        }
    }

    #[Route(path: '/workflow/ajax/create-show', name: 'app_workflow_ajax_create_show', options: ['expose' => true])]
    public function createShowAjax(Request $request, EntityManagerInterface $entityManager): Response
    {
        $show = new Show();
        $payload = $request->getPayload();
        if (!empty($payload->get('showName'))) {
            $show->setName($payload->get('showName'));
            $entityManager->persist($show);
            $entityManager->flush();
            return $this->json(['id' => $show->getId(), 'showName' => $show->getName(), 'status' => 'success']);
        }

        throw $this->createNotFoundException();
    }

    #[Route(path: '/workflow/ajax/owner/{id}', name: 'app_workflow_ajax_get_owner', options: ['expose' => true])]
    public function getOwnerAjax(Show $show)
    {
        return $this->json(['id' => $show->getOwner()->getId(), 'fullName' => $show->getOwner()->getFullname()]);
    }

    #[Route(path: '/workflow/ajax/associated-workflows/{id}', name: 'app_workflow_ajax_get_associated_workflows', options: ['expose' => true])]
    public function getAssociatedWorkflowsAjax(Show $show, ContractRepository $contractRepository)
    {
        $workflowReadyContracts = $contractRepository->getWorkflowReadyContractsForShow($show);
        return $this->json([
            'workflows' => $show->getWorkflows()
                ->filter(fn(Workflow $workflow) => !$workflow->isClosed())
                ->map(function (Workflow $workflow) {
                    return ['id' => $workflow->getId(), 'name' => $workflow->__toString()];
                })
                ->getValues(),
            'contracts' => array_map(fn(Contract $contract) => ['id' => $contract->getId(), 'name' => $contract->__toString()], $workflowReadyContracts)
        ]);
    }
}
