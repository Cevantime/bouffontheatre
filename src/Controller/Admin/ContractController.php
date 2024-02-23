<?php

namespace App\Controller\Admin;

use App\DTO\ContractCompanyPart;
use App\DTO\ContractGlobalConfig;
use App\DTO\ContractTheaterPart;
use App\Entity\Contract;
use App\Entity\Project;
use App\Entity\Show;
use App\Form\ContractGlobalConfigType;
use App\Form\ContractTheaterPartType;
use App\Repository\ContractRepository;
use App\Service\ContractService;
use App\Service\DTOService;
use App\Service\ConfigService;
use App\Service\StringCallbacks;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Phalcon\Forms\Element\Date;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\String\u;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class ContractController extends AbstractController
{

    #[Route(path: '/contract/config', name: 'app_contract_config')]
    public function form(
        Request                   $request,
        ConfigService             $configService,
        DTOService $DTOService
    ): Response
    {
        $contractConfig = new ContractGlobalConfig();
        $configs = $configService->getRawConfigs();
        $DTOService->transferDataTo($configs, $contractConfig, StringCallbacks::class.'::camelize');
        $form = $this->createForm(ContractGlobalConfigType::class, $contractConfig);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $DTOService->transferDataTo($contractConfig, $configs, StringCallbacks::class.'::snakify');
            $configService->saveConfigs($configs);
        }
        return $this->render('sonata/contract/config.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/contract/from-project/{id}', name: 'app_contract_create_from_project', options: ['expose' => true])]
    #[Route(path: '/contract/from-project/{id}/edit/{idContract}', name: 'app_contract_edit_from_project', options: ['expose' => true])]
    public function createContractForProject(
        ConfigService $configService,
        DTOService $DTOService,
        Request $request,
        EntityManagerInterface $entityManager,
        ContractRepository $contractRepository,
        ContractService $contractService,
        ?Project $project = null,
        $idContract = null
    )
    {
        $contractTheaterPart = new ContractTheaterPart();
        $contractCompanyPart = new ContractCompanyPart();
        $contractConfig = new ContractGlobalConfig();
        if($idContract) {
            $contract = $contractRepository->find($idContract);
            if(!$contract){
                throw $this->createNotFoundException();
            }
            $DTOService->transferDataTo($contract, $contractTheaterPart);
            $DTOService->transferDataTo($contract, $contractCompanyPart);
            $DTOService->transferDataTo($contract, $contractConfig);
        } else {
            $contract = new Contract();
        }

        $contractTheaterPart->project = $project;
        $contractTheaterPart->contractConfig = $contractConfig;
        $contractTheaterPart->contractCompanyPart = $contractCompanyPart;
        $lastContract = $contractRepository->getUserLastCompletedContract($project->getOwner());
        if($lastContract && !$idContract) {
            $DTOService->transferDataTo($lastContract, $contractCompanyPart);
            $DTOService->transferDataTo($lastContract, $contractConfig);
        } elseif(!$idContract) {
            $configs = $configService->getRawConfigs();
            $DTOService->transferDataTo($configs, $contractConfig, StringCallbacks::class.'::camelize');
        }
        $form = $this->createForm(ContractTheaterPartType::class, $contractTheaterPart);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contractDate = new \DateTimeImmutable();
            $contract->setContractDate($contractDate);
            $contractSignatureDate = $contractDate->add(\DateInterval::createFromDateString('14 day'));
            $contract->setContractSignatureDate($contractSignatureDate);
            foreach ($contractTheaterPart->performances as $performance) {
                $performance->setRelatedProject($contractTheaterPart->project);
                $contract->addPerformance($performance);
                $entityManager->persist($performance);
            }
            $contract->setRelatedProject($contractTheaterPart->project);
            $DTOService->transferDataTo($contractCompanyPart, $contract);
            $DTOService->transferDataTo($contractTheaterPart, $contract);
            $DTOService->transferDataTo($contractConfig, $contract);
            $entityManager->persist($contract);
            $entityManager->flush();
            if($request->request->get('invite')) {
                return $this->redirectToRoute('app_contract_invite_company', ['id' => $contract->getId()]);
            } elseif ($request->request->get('generate')) {
                return $contractService->createGeneratedContractResponse($contract);
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

    #[Route(path: '/contract/invite-company/{id}', name:'app_contract_invite_company')]
    public function inviteCompany(
        Contract $contract,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    )
    {
        $contract->setStatus(Contract::STATUS_SENT_TO_COMPANY);
        $entityManager->persist($contract);
        $entityManager->flush();
        $user = $contract->getRelatedProject()->getOwner();
        $mail = (new TemplatedEmail())
            ->from('contactbouffon@gmail.com')
            ->to(new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()))
            ->subject('Fiche infos Bouffon Théâtre')
            ->htmlTemplate('front/user/email_user_contract_fill.html.twig')
            ->context(['user' => $user]);

        $mailer->send($mail);
        $this->addFlash('success', 'Formulaire envoyé avec succès');
        return $this->redirectToRoute('admin_app_contract_show', ['id' => $contract->getId()]);
    }

    #[Route('/contract/generate/{id}', name: 'app_contract_generate')]
    public function generateContract(Contract $contract, ContractService $contractService)
    {
        return $contractService->createGeneratedContractResponse($contract);
    }

    #[Route('/contract/send-email/{id}', name: 'app_contract_send_email')]
    public function sendByEmail(Contract $contract, MailerInterface $mailer, ContractService $contractService)
    {
        $user = $contract->getRelatedProject()->getOwner();

        $export = $contractService->generateContractFile($contract);
        $mail = (new TemplatedEmail())
            ->from('contactbouffon@gmail.com')
            ->to(new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()))
            ->subject('Contrat Bouffon Théâtre')
            ->attach(fopen($export['path'], 'r'), $export['name'])
            ->htmlTemplate('front/user/email_user_contract_sign.html.twig')
            ->context(['user' => $user]);
        $mailer->send($mail);
        $this->addFlash('success', 'Contrat envoyé avec succès');
        return $this->redirectToRoute('admin_app_contract_show', ['id' => $contract->getId()]);
    }


}
