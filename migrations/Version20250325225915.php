<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325225915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow ADD revenue_tick_boss_pdf_id INT DEFAULT NULL, ADD copyright_applicable TINYINT(1) DEFAULT NULL, ADD retirement_contrib_applicable TINYINT(1) DEFAULT NULL, ADD agessa_contrib_applicable TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C5981657CDD30E FOREIGN KEY (revenue_tick_boss_pdf_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65C5981657CDD30E ON workflow (revenue_tick_boss_pdf_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C5981657CDD30E');
        $this->addSql('DROP INDEX UNIQ_65C5981657CDD30E ON workflow');
        $this->addSql('ALTER TABLE workflow DROP revenue_tick_boss_pdf_id, DROP copyright_applicable, DROP retirement_contrib_applicable, DROP agessa_contrib_applicable');
    }
}
