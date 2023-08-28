<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114150200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6ABE04EA9');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AB7970CF8');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6ABE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AB7970CF8');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6ABE04EA9');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist_item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6ABE04EA9 FOREIGN KEY (job_id) REFERENCES job_item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
