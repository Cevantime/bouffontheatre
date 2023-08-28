<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206140650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687ADA40271');
        $this->addSql('DROP INDEX UNIQ_1599687ADA40271 ON artist');
        $this->addSql('ALTER TABLE artist DROP link_id');
        $this->addSql('ALTER TABLE link_item ADD artist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE link_item ADD CONSTRAINT FK_AE5CB7B7B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('CREATE INDEX IDX_AE5CB7B7B7970CF8 ON link_item (artist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD link_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1599687ADA40271 ON artist (link_id)');
        $this->addSql('ALTER TABLE link_item DROP FOREIGN KEY FK_AE5CB7B7B7970CF8');
        $this->addSql('DROP INDEX IDX_AE5CB7B7B7970CF8 ON link_item');
        $this->addSql('ALTER TABLE link_item DROP artist_id');
    }
}
