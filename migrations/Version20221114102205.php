<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114102205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paper_item (id INT AUTO_INCREMENT NOT NULL, paper_id INT NOT NULL, project_id INT NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_E9F38409E6758861 (paper_id), INDEX IDX_E9F38409166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paper_item ADD CONSTRAINT FK_E9F38409E6758861 FOREIGN KEY (paper_id) REFERENCES paper (id)');
        $this->addSql('ALTER TABLE paper_item ADD CONSTRAINT FK_E9F38409166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paper_item DROP FOREIGN KEY FK_E9F38409E6758861');
        $this->addSql('ALTER TABLE paper_item DROP FOREIGN KEY FK_E9F38409166D1F9C');
        $this->addSql('DROP TABLE paper_item');
    }
}
