<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203121906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD featured_document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEC5572ED6 FOREIGN KEY (featured_document_id) REFERENCES media_gallery (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEC5572ED6 ON project (featured_document_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEC5572ED6');
        $this->addSql('DROP INDEX IDX_2FB3D0EEC5572ED6 ON project');
        $this->addSql('ALTER TABLE project DROP featured_document_id');
    }
}
