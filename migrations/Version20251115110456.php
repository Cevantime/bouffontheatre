<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251115110456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD price VARCHAR(60) NOT NULL DEFAULT \'Non précisé\', ADD comment LONGTEXT DEFAULT NULL, CHANGE tarif1 place_count INT DEFAULT 0, DROP tarif2, DROP tarif3, DROP tarif4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD tarif2 INT DEFAULT NULL, ADD tarif3 INT DEFAULT NULL, ADD tarif4 INT DEFAULT NULL, DROP price, drop comment, CHANGE place_count tarif1 INT DEFAULT NULL');
    }
}
