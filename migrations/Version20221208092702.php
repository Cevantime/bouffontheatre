<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221208092702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_artist DROP FOREIGN KEY FK_696FE62B84A0A3ED');
        $this->addSql('ALTER TABLE content_artist DROP FOREIGN KEY FK_696FE62BB7970CF8');
        $this->addSql('DROP TABLE content_artist');
        $this->addSql('ALTER TABLE artist_item ADD content_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist_item ADD CONSTRAINT FK_A3A5A38184A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('CREATE INDEX IDX_A3A5A38184A0A3ED ON artist_item (content_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_artist (content_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_696FE62BB7970CF8 (artist_id), INDEX IDX_696FE62B84A0A3ED (content_id), PRIMARY KEY(content_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE content_artist ADD CONSTRAINT FK_696FE62B84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_artist ADD CONSTRAINT FK_696FE62BB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_item DROP FOREIGN KEY FK_A3A5A38184A0A3ED');
        $this->addSql('DROP INDEX IDX_A3A5A38184A0A3ED ON artist_item');
        $this->addSql('ALTER TABLE artist_item DROP content_id');
    }
}
