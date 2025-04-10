<?php

namespace App\Service;

use App\Contract\ContractGenerator;
use App\DTO\Export;
use App\DTO\RevenueExport;
use App\Entity\Contract;
use App\Entity\Workflow;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private ContractGenerator $contractGenerator,
        private Pool $pool
    )
    {
    }

    public function sendEmailToAdmins($subject, $template, $params = [])
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@bouffon.fr', 'Le site du bouffon'))
            ->to('contactbouffon@gmail.com')
            ->subject('Admin Bouffon : ' . $subject)
            ->htmlTemplate($template)
            ->context($params)
        ;

        $this->mailer->send($email);
    }

    public function sendMailTo($address, $subject, $template, $params = [])
    {
        $this->mailer->send($this->buildClientEmail($address, $subject, $template, $params));
    }

    public function sendContractMail(Contract $contract)
    {
        $export = $this->contractGenerator->generateContractFile($contract);
        $user = $contract->getRelatedProject()->getOwner();
        $email = $this->buildClientEmail(new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()),
            'Contrat Bouffon Théâtre',
            'front/user/email_user_contract_sign.html.twig',
            ['user' => $user]
        );
        $email->attach(fopen($export->path, 'r'), $export->name);
        $this->mailer->send($email);
    }

    public function sendFetchDataFormMail(Contract $contract)
    {
        $user = $contract->getRelatedProject()->getOwner();
        $email = $this->buildClientEmail(
            new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname()),
            'Fiche infos Bouffon Théâtre',
            'front/user/email_user_contract_fill.html.twig',
            ['user' => $user],
        );
        $this->mailer->send($email);
    }


    public function sendRevenueEmailToPresident(Workflow $workflow, RevenueExport $revenueExport)
    {
        $email = $this->buildClientEmail(
            new Address('contact@bouffontheatre.fr', "Bouffon Théâtre"),
            sprintf('Virement compagnie %s', $workflow->getAssociatedShow()->getName()),
            'sonata/workflow/richard_email.html.twig',
            ['workflow' => $workflow, 'companyRevenue' => $revenueExport->companyRevenue],
        );

        $email->attach(fopen($revenueExport->path, 'r'), $revenueExport->name);

        $tickbossPdfMedia = $workflow->getRevenueTickBossPdf();

        $provider = $this->pool->getProvider($tickbossPdfMedia->getProviderName());
        $pdfContent = $provider->getReferenceFile($tickbossPdfMedia)->getContent();

        $email->attach($pdfContent, 'Relevé de billeterie.pdf', 'application/pdf');

        $this->mailer->send($email);
    }

    public function sendRevenueEmailToCompany(Workflow $workflow, RevenueExport $revenueExport)
    {
        $owner = $workflow->getAssociatedShow()->getOwner();
        $email = $this->buildClientEmail(
            new Address($owner->getEmail(), $owner->getFullname()),
            sprintf('Relevé de recette %s', $workflow->getAssociatedShow()->getName()),
            'sonata/workflow/company_email.html.twig',
            ['workflow' => $workflow, 'companyRevenue' => $revenueExport->companyRevenue],
        );

        $email->attach(fopen($revenueExport->path, 'r'), $revenueExport->name);

        $tickbossPdfMedia = $workflow->getRevenueTickBossPdf();

        $provider = $this->pool->getProvider($tickbossPdfMedia->getProviderName());
        $pdfContent = $provider->getReferenceFile($tickbossPdfMedia)->getContent();

        $email->attach($pdfContent, 'Relevé de billeterie.pdf', 'application/pdf');

        $this->mailer->send($email);
    }

    private function buildClientEmail($address, $subject, $template, $params = [])
    {
        return (new TemplatedEmail())
            ->from(new Address('contactbouffon@gmail.com', 'Le Bouffon Théâtre'))
            ->to($address)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($params)
            ;
    }
}
