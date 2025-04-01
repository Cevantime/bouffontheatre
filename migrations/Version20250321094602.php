<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321094602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow ADD associated_show_id INT NOT NULL');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C59816CB880320 FOREIGN KEY (associated_show_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_65C59816CB880320 ON workflow (associated_show_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C59816CB880320');
        $this->addSql('DROP INDEX IDX_65C59816CB880320 ON workflow');
        $this->addSql('ALTER TABLE workflow DROP associated_show_id');
    }
}
