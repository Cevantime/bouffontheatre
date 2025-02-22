<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219090557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project CHANGE bookable_online bookable_online TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD first_name VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE `name` last_name VARCHAR(60) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project CHANGE bookable_online bookable_online TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE reservation DROP first_name');
        $this->addSql('ALTER TABLE reservation CHANGE last_name `name` VARCHAR(255) NOT NULL');
    }
}
