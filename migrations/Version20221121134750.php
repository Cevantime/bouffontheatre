<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121134750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP INDEX UNIQ_2FB3D0EE4E7AF8F, ADD INDEX IDX_2FB3D0EE4E7AF8F (gallery_id)');
        $this->addSql('ALTER TABLE project ADD discr VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP INDEX IDX_2FB3D0EE4E7AF8F, ADD UNIQUE INDEX UNIQ_2FB3D0EE4E7AF8F (gallery_id)');
        $this->addSql('ALTER TABLE project DROP discr');
    }
}
