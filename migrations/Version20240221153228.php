<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221153228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, related_project_id INT DEFAULT NULL, theater_name VARCHAR(150) DEFAULT NULL, theater_address LONGTEXT DEFAULT NULL, theater_siret VARCHAR(20) DEFAULT NULL, theater_president VARCHAR(150) DEFAULT NULL, theater_email VARCHAR(255) DEFAULT NULL, theater_phone VARCHAR(20) DEFAULT NULL, company_name VARCHAR(150) DEFAULT NULL, company_siret VARCHAR(20) DEFAULT NULL, company_ape VARCHAR(10) DEFAULT NULL, company_license VARCHAR(100) DEFAULT NULL, company_president VARCHAR(150) DEFAULT NULL, company_address LONGTEXT DEFAULT NULL, company_assurance VARCHAR(100) DEFAULT NULL, company_phone VARCHAR(15) DEFAULT NULL, show_name VARCHAR(150) DEFAULT NULL, show_author VARCHAR(150) DEFAULT NULL, show_director VARCHAR(150) DEFAULT NULL, show_artist_count INT DEFAULT NULL, show_duration INT DEFAULT NULL, show_theater_availability VARCHAR(100) DEFAULT NULL, theater_booking_phone VARCHAR(15) DEFAULT NULL, show_full_price NUMERIC(5, 2) DEFAULT NULL, show_half_price NUMERIC(5, 2) DEFAULT NULL, show_max_duration INT DEFAULT NULL, show_invitations VARCHAR(255) DEFAULT NULL, show_theater_share NUMERIC(5, 2) DEFAULT NULL, show_company_share NUMERIC(5, 2) DEFAULT NULL, show_company_share_percent NUMERIC(5, 2) DEFAULT NULL, show_theater_share_percent NUMERIC(5, 2) DEFAULT NULL, show_minimum_share NUMERIC(5, 2) DEFAULT NULL, show_service_session VARCHAR(255) DEFAULT NULL, show_rib VARCHAR(50) DEFAULT NULL, contract_city VARCHAR(100) DEFAULT NULL, contract_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', contract_signature_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', tva NUMERIC(5, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_E98F28599CA0172C (related_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE performance (id INT AUTO_INCREMENT NOT NULL, related_project_id INT DEFAULT NULL, contract_id INT DEFAULT NULL, performed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_82D796819CA0172C (related_project_id), INDEX IDX_82D796812576E0FD (contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F28599CA0172C FOREIGN KEY (related_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE performance ADD CONSTRAINT FK_82D796819CA0172C FOREIGN KEY (related_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE performance ADD CONSTRAINT FK_82D796812576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F28599CA0172C');
        $this->addSql('ALTER TABLE performance DROP FOREIGN KEY FK_82D796819CA0172C');
        $this->addSql('ALTER TABLE performance DROP FOREIGN KEY FK_82D796812576E0FD');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE performance');
    }
}
