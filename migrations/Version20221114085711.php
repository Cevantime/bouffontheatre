<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114085711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD banner_id INT DEFAULT NULL, ADD gallery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE684EC833 FOREIGN KEY (banner_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE4E7AF8F FOREIGN KEY (gallery_id) REFERENCES media_gallery (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE684EC833 ON project (banner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE4E7AF8F ON project (gallery_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE684EC833');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE4E7AF8F');
        $this->addSql('DROP INDEX IDX_2FB3D0EE684EC833 ON project');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE4E7AF8F ON project');
        $this->addSql('ALTER TABLE project DROP banner_id, DROP gallery_id');
    }
}
