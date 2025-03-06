<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306102023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract ADD contract_type VARCHAR(150) NOT NULL, ADD minimum_share TINYINT(1) NOT NULL, ADD stage_management_install_hour_count INT DEFAULT NULL, ADD stage_management_show_hour_count INT DEFAULT NULL, ADD stage_management_show_price NUMERIC(10, 2) NOT NULL, ADD stage_management_install_price NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP contract_type, DROP minimum_share, DROP stage_management_install_hour_count, DROP stage_management_show_hour_count, DROP stage_management_show_price, DROP stage_management_install_price');
    }
}
