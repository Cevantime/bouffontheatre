<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114095450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_item (id INT AUTO_INCREMENT NOT NULL, link_id INT NOT NULL, project_id INT DEFAULT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_AE5CB7B7ADA40271 (link_id), INDEX IDX_AE5CB7B7166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_item ADD CONSTRAINT FK_AE5CB7B7ADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE link_item ADD CONSTRAINT FK_AE5CB7B7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1166D1F9C');
        $this->addSql('DROP INDEX IDX_36AC99F1166D1F9C ON link');
        $this->addSql('ALTER TABLE link DROP project_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_item DROP FOREIGN KEY FK_AE5CB7B7ADA40271');
        $this->addSql('ALTER TABLE link_item DROP FOREIGN KEY FK_AE5CB7B7166D1F9C');
        $this->addSql('DROP TABLE link_item');
        $this->addSql('ALTER TABLE link ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_36AC99F1166D1F9C ON link (project_id)');
    }
}
