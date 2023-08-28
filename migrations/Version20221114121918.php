<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114121918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE period_item (id INT AUTO_INCREMENT NOT NULL, period_id INT NOT NULL, project_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D91DAFB0EC8B7ADE (period_id), INDEX IDX_D91DAFB0166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE period_item ADD CONSTRAINT FK_D91DAFB0EC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE period_item ADD CONSTRAINT FK_D91DAFB0166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECE166D1F9C');
        $this->addSql('DROP INDEX IDX_C5B81ECE166D1F9C ON period');
        $this->addSql('ALTER TABLE period DROP project_id, CHANGE days days LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE period_item DROP FOREIGN KEY FK_D91DAFB0EC8B7ADE');
        $this->addSql('ALTER TABLE period_item DROP FOREIGN KEY FK_D91DAFB0166D1F9C');
        $this->addSql('DROP TABLE period_item');
        $this->addSql('ALTER TABLE period ADD project_id INT DEFAULT NULL, CHANGE days days LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C5B81ECE166D1F9C ON period (project_id)');
    }
}
