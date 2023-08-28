<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123182842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_item_show DROP FOREIGN KEY FK_E6EAF672A5200C69');
        $this->addSql('ALTER TABLE artist_item_show DROP FOREIGN KEY FK_E6EAF672D0C1FC64');
        $this->addSql('DROP TABLE artist_item_show');
        $this->addSql('ALTER TABLE artist_item ADD authored_show_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist_item ADD CONSTRAINT FK_A3A5A381E218C3AE FOREIGN KEY (authored_show_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_A3A5A381E218C3AE ON artist_item (authored_show_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_item_show (artist_item_id INT NOT NULL, show_id INT NOT NULL, INDEX IDX_E6EAF672D0C1FC64 (show_id), INDEX IDX_E6EAF672A5200C69 (artist_item_id), PRIMARY KEY(artist_item_id, show_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE artist_item_show ADD CONSTRAINT FK_E6EAF672A5200C69 FOREIGN KEY (artist_item_id) REFERENCES artist_item (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_item_show ADD CONSTRAINT FK_E6EAF672D0C1FC64 FOREIGN KEY (show_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_item DROP FOREIGN KEY FK_A3A5A381E218C3AE');
        $this->addSql('DROP INDEX IDX_A3A5A381E218C3AE ON artist_item');
        $this->addSql('ALTER TABLE artist_item DROP authored_show_id');
    }
}
