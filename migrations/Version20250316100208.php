<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316100208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_item DROP FOREIGN KEY FK_A3A5A38184A0A3ED');
        $this->addSql('DROP INDEX IDX_A3A5A38184A0A3ED ON artist_item');
        $this->addSql('ALTER TABLE artist_item DROP content_id');
        $this->addSql('ALTER TABLE media CHANGE content_size content_size BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EA76ED395');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EB7970CF8');
        $this->addSql('DROP INDEX IDX_FEFDAB8EA76ED395 ON view');
        $this->addSql('DROP INDEX IDX_FEFDAB8EB7970CF8 ON view');
        $this->addSql('ALTER TABLE view DROP user_id, DROP artist_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_item ADD content_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist_item ADD CONSTRAINT FK_A3A5A38184A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A3A5A38184A0A3ED ON artist_item (content_id)');
        $this->addSql('ALTER TABLE media CHANGE content_size content_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE view ADD user_id INT NOT NULL, ADD artist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FEFDAB8EA76ED395 ON view (user_id)');
        $this->addSql('CREATE INDEX IDX_FEFDAB8EB7970CF8 ON view (artist_id)');
    }
}
