<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206143836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_job (artist_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_23DD6549B7970CF8 (artist_id), INDEX IDX_23DD6549BE04EA9 (job_id), PRIMARY KEY(artist_id, job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_job ADD CONSTRAINT FK_23DD6549B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_job ADD CONSTRAINT FK_23DD6549BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_job DROP FOREIGN KEY FK_23DD6549B7970CF8');
        $this->addSql('ALTER TABLE artist_job DROP FOREIGN KEY FK_23DD6549BE04EA9');
        $this->addSql('DROP TABLE artist_job');
    }
}
