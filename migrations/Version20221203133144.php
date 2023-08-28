<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203133144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_item (id INT AUTO_INCREMENT NOT NULL, media_id INT NOT NULL, name VARCHAR(150) NOT NULL, INDEX IDX_DC5CFACDEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_item ADD CONSTRAINT FK_DC5CFACDEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEC5572ED6');
        $this->addSql('DROP INDEX IDX_2FB3D0EEC5572ED6 ON project');
        $this->addSql('ALTER TABLE project DROP featured_document_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_item DROP FOREIGN KEY FK_DC5CFACDEA9FDD75');
        $this->addSql('DROP TABLE media_item');
        $this->addSql('ALTER TABLE project ADD featured_document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEC5572ED6 FOREIGN KEY (featured_document_id) REFERENCES media_gallery (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEC5572ED6 ON project (featured_document_id)');
    }
}
