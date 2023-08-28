<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113142704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_item (id INT AUTO_INCREMENT NOT NULL, artist_id INT NOT NULL, acted_project_id INT DEFAULT NULL, directed_project_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_A3A5A381B7970CF8 (artist_id), INDEX IDX_A3A5A381FD3B24B5 (acted_project_id), INDEX IDX_A3A5A381321DF3DF (directed_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_item ADD CONSTRAINT FK_A3A5A381B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE artist_item ADD CONSTRAINT FK_A3A5A381FD3B24B5 FOREIGN KEY (acted_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE artist_item ADD CONSTRAINT FK_A3A5A381321DF3DF FOREIGN KEY (directed_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE acted_project_artist DROP FOREIGN KEY FK_38C0562AB7970CF8');
        $this->addSql('ALTER TABLE acted_project_artist DROP FOREIGN KEY FK_38C0562A166D1F9C');
        $this->addSql('ALTER TABLE directed_project_artist DROP FOREIGN KEY FK_2C983EFDB7970CF8');
        $this->addSql('ALTER TABLE directed_project_artist DROP FOREIGN KEY FK_2C983EFD166D1F9C');
        $this->addSql('DROP TABLE acted_project_artist');
        $this->addSql('DROP TABLE directed_project_artist');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acted_project_artist (project_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_38C0562AB7970CF8 (artist_id), INDEX IDX_38C0562A166D1F9C (project_id), PRIMARY KEY(project_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE directed_project_artist (project_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_2C983EFD166D1F9C (project_id), INDEX IDX_2C983EFDB7970CF8 (artist_id), PRIMARY KEY(project_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE acted_project_artist ADD CONSTRAINT FK_38C0562AB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acted_project_artist ADD CONSTRAINT FK_38C0562A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE directed_project_artist ADD CONSTRAINT FK_2C983EFDB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE directed_project_artist ADD CONSTRAINT FK_2C983EFD166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_item DROP FOREIGN KEY FK_A3A5A381B7970CF8');
        $this->addSql('ALTER TABLE artist_item DROP FOREIGN KEY FK_A3A5A381FD3B24B5');
        $this->addSql('ALTER TABLE artist_item DROP FOREIGN KEY FK_A3A5A381321DF3DF');
        $this->addSql('DROP TABLE artist_item');
    }
}
