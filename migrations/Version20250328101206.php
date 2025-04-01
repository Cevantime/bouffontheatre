<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328101206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract ADD show_tax_free_price NUMERIC(5, 2) NOT NULL, ADD show_app_price NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE performance ADD tax_free_count INT DEFAULT NULL, ADD app_count INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP show_tax_free_price, DROP show_app_price');
        $this->addSql('ALTER TABLE performance DROP tax_free_count, DROP app_count');
    }
}
