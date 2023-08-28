<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113112021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_gallery_item ADD gallery_id INT DEFAULT NULL, ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media_gallery_item ADD CONSTRAINT FK_E5F5A71E4E7AF8F FOREIGN KEY (gallery_id) REFERENCES media_gallery (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_gallery_item ADD CONSTRAINT FK_E5F5A71EEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E5F5A71E4E7AF8F ON media_gallery_item (gallery_id)');
        $this->addSql('CREATE INDEX IDX_E5F5A71EEA9FDD75 ON media_gallery_item (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_gallery_item DROP FOREIGN KEY FK_E5F5A71E4E7AF8F');
        $this->addSql('ALTER TABLE media_gallery_item DROP FOREIGN KEY FK_E5F5A71EEA9FDD75');
        $this->addSql('DROP INDEX IDX_E5F5A71E4E7AF8F ON media_gallery_item');
        $this->addSql('DROP INDEX IDX_E5F5A71EEA9FDD75 ON media_gallery_item');
        $this->addSql('ALTER TABLE media_gallery_item DROP gallery_id, DROP media_id');
    }
}
