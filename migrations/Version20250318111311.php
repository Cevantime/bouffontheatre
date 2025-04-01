<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318111311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow ADD contract_id INT NOT NULL, ADD show_highlighted TINYINT(1) NOT NULL, ADD show_removed TINYINT(1) NOT NULL, ADD revenue_reported TINYINT(1) NOT NULL, ADD sibil_done TINYINT(1) NOT NULL, ADD dectanet_done TINYINT(1) NOT NULL, ADD emails_sent TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C598162576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65C598162576E0FD ON workflow (contract_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C598162576E0FD');
        $this->addSql('DROP INDEX UNIQ_65C598162576E0FD ON workflow');
        $this->addSql('ALTER TABLE workflow DROP contract_id, DROP show_highlighted, DROP show_removed, DROP revenue_reported, DROP sibil_done, DROP dectanet_done, DROP emails_sent');
    }
}
