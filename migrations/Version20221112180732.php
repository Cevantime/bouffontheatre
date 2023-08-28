<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221112180732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, associated_user_id INT DEFAULT NULL, link_id INT DEFAULT NULL, firstname VARCHAR(60) NOT NULL, lastname VARCHAR(60) NOT NULL, stage_name VARCHAR(60) DEFAULT NULL, has_file TINYINT(1) NOT NULL, biography LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1599687BC272CD1 (associated_user_id), UNIQUE INDEX UNIQ_1599687ADA40271 (link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery_item (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_36AC99F1166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, provider_name VARCHAR(255) NOT NULL, provider_status INT NOT NULL, provider_reference VARCHAR(255) NOT NULL, provider_metadata JSON DEFAULT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, length NUMERIC(10, 0) DEFAULT NULL, content_type VARCHAR(255) DEFAULT NULL, content_size INT DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, author_name VARCHAR(255) DEFAULT NULL, context VARCHAR(64) DEFAULT NULL, cdn_is_flushable TINYINT(1) NOT NULL, cdn_flush_identifier VARCHAR(64) DEFAULT NULL, cdn_flush_at DATETIME DEFAULT NULL, cdn_status INT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, context VARCHAR(64) NOT NULL, default_format VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paper (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, link_id INT NOT NULL, extract LONGTEXT NOT NULL, INDEX IDX_4E1A6016166D1F9C (project_id), UNIQUE INDEX UNIQ_4E1A6016ADA40271 (link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, days LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_C5B81ECE166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pro_contact (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) DEFAULT NULL, stage_name VARCHAR(60) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pro_contact_job (pro_contact_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_D087549954D794EA (pro_contact_id), INDEX IDX_D0875499BE04EA9 (job_id), PRIMARY KEY(pro_contact_id, job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directed_project_artist (project_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_2C983EFD166D1F9C (project_id), INDEX IDX_2C983EFDB7970CF8 (artist_id), PRIMARY KEY(project_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acted_project_artist (project_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_38C0562A166D1F9C (project_id), INDEX IDX_38C0562AB7970CF8 (artist_id), PRIMARY KEY(project_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, artist_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_57698A6ABE04EA9 (job_id), INDEX IDX_57698A6AB7970CF8 (artist_id), INDEX IDX_57698A6A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_artist (user_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_640B8DBAA76ED395 (user_id), INDEX IDX_640B8DBAB7970CF8 (artist_id), PRIMARY KEY(user_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687BC272CD1 FOREIGN KEY (associated_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687ADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE paper ADD CONSTRAINT FK_4E1A6016166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE paper ADD CONSTRAINT FK_4E1A6016ADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE pro_contact_job ADD CONSTRAINT FK_D087549954D794EA FOREIGN KEY (pro_contact_id) REFERENCES pro_contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pro_contact_job ADD CONSTRAINT FK_D0875499BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE directed_project_artist ADD CONSTRAINT FK_2C983EFD166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE directed_project_artist ADD CONSTRAINT FK_2C983EFDB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acted_project_artist ADD CONSTRAINT FK_38C0562A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acted_project_artist ADD CONSTRAINT FK_38C0562AB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6ABE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687BC272CD1');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687ADA40271');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1166D1F9C');
        $this->addSql('ALTER TABLE paper DROP FOREIGN KEY FK_4E1A6016166D1F9C');
        $this->addSql('ALTER TABLE paper DROP FOREIGN KEY FK_4E1A6016ADA40271');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECE166D1F9C');
        $this->addSql('ALTER TABLE pro_contact_job DROP FOREIGN KEY FK_D087549954D794EA');
        $this->addSql('ALTER TABLE pro_contact_job DROP FOREIGN KEY FK_D0875499BE04EA9');
        $this->addSql('ALTER TABLE directed_project_artist DROP FOREIGN KEY FK_2C983EFD166D1F9C');
        $this->addSql('ALTER TABLE directed_project_artist DROP FOREIGN KEY FK_2C983EFDB7970CF8');
        $this->addSql('ALTER TABLE acted_project_artist DROP FOREIGN KEY FK_38C0562A166D1F9C');
        $this->addSql('ALTER TABLE acted_project_artist DROP FOREIGN KEY FK_38C0562AB7970CF8');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6ABE04EA9');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AB7970CF8');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6A166D1F9C');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAA76ED395');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAB7970CF8');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE gallery_item');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE media_gallery');
        $this->addSql('DROP TABLE paper');
        $this->addSql('DROP TABLE period');
        $this->addSql('DROP TABLE pro_contact');
        $this->addSql('DROP TABLE pro_contact_job');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE directed_project_artist');
        $this->addSql('DROP TABLE acted_project_artist');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_artist');
    }
}
