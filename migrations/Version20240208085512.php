<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208085512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE insight (id INT AUTO_INCREMENT NOT NULL, related_show_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(30) NOT NULL, metadata JSON DEFAULT NULL, html LONGTEXT NOT NULL, INDEX IDX_FE3413DBEFD836C1 (related_show_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE insight ADD CONSTRAINT FK_FE3413DBEFD836C1 FOREIGN KEY (related_show_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE insight DROP FOREIGN KEY FK_FE3413DBEFD836C1');
        $this->addSql('DROP TABLE insight');
    }
}
