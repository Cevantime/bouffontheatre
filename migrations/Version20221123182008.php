<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123182008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE show_artist_item (show_id INT NOT NULL, artist_item_id INT NOT NULL, INDEX IDX_CA153C9D0C1FC64 (show_id), INDEX IDX_CA153C9A5200C69 (artist_item_id), PRIMARY KEY(show_id, artist_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE show_artist_item ADD CONSTRAINT FK_CA153C9D0C1FC64 FOREIGN KEY (show_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE show_artist_item ADD CONSTRAINT FK_CA153C9A5200C69 FOREIGN KEY (artist_item_id) REFERENCES artist_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE show_artist DROP FOREIGN KEY FK_8E3EB91CB7970CF8');
        $this->addSql('ALTER TABLE show_artist DROP FOREIGN KEY FK_8E3EB91CD0C1FC64');
        $this->addSql('DROP TABLE show_artist');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE show_artist (show_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_8E3EB91CD0C1FC64 (show_id), INDEX IDX_8E3EB91CB7970CF8 (artist_id), PRIMARY KEY(show_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE show_artist ADD CONSTRAINT FK_8E3EB91CB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE show_artist ADD CONSTRAINT FK_8E3EB91CD0C1FC64 FOREIGN KEY (show_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE show_artist_item DROP FOREIGN KEY FK_CA153C9D0C1FC64');
        $this->addSql('ALTER TABLE show_artist_item DROP FOREIGN KEY FK_CA153C9A5200C69');
        $this->addSql('DROP TABLE show_artist_item');
    }
}
