<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221112181554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_gallery_item (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE gallery_item');
        $this->addSql('ALTER TABLE artist ADD photo_id INT DEFAULT NULL, ADD gallery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_15996877E9E4C8C FOREIGN KEY (photo_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_15996874E7AF8F FOREIGN KEY (gallery_id) REFERENCES media_gallery (id)');
        $this->addSql('CREATE INDEX IDX_15996877E9E4C8C ON artist (photo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15996874E7AF8F ON artist (gallery_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallery_item (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE media_gallery_item');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_15996877E9E4C8C');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_15996874E7AF8F');
        $this->addSql('DROP INDEX IDX_15996877E9E4C8C ON artist');
        $this->addSql('DROP INDEX UNIQ_15996874E7AF8F ON artist');
        $this->addSql('ALTER TABLE artist DROP photo_id, DROP gallery_id');
    }
}
