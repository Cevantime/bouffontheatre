<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114143530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role_item (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, project_id INT NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_A1A4578AD60322AC (role_id), INDEX IDX_A1A4578A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_item ADD CONSTRAINT FK_A1A4578AD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE role_item ADD CONSTRAINT FK_A1A4578A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_item DROP FOREIGN KEY FK_A1A4578AD60322AC');
        $this->addSql('ALTER TABLE role_item DROP FOREIGN KEY FK_A1A4578A166D1F9C');
        $this->addSql('DROP TABLE role_item');
    }
}
