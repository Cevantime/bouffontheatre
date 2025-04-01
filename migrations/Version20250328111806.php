<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328111806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE performance ADD tax_free_price_count INT DEFAULT NULL, ADD app_price_count INT DEFAULT NULL, DROP tax_free_count, DROP app_count');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE performance ADD tax_free_count INT DEFAULT NULL, ADD app_count INT DEFAULT NULL, DROP tax_free_price_count, DROP app_price_count');
    }
}
