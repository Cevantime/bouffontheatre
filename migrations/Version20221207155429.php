<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221207155429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_project_item DROP FOREIGN KEY FK_2202E1B984715E3B');
        $this->addSql('ALTER TABLE content_project_item DROP FOREIGN KEY FK_2202E1B984A0A3ED');
        $this->addSql('DROP TABLE content_project_item');
        $this->addSql('ALTER TABLE project_item ADD content_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_item ADD CONSTRAINT FK_268AED0684A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('CREATE INDEX IDX_268AED0684A0A3ED ON project_item (content_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_project_item (content_id INT NOT NULL, project_item_id INT NOT NULL, INDEX IDX_2202E1B984715E3B (project_item_id), INDEX IDX_2202E1B984A0A3ED (content_id), PRIMARY KEY(content_id, project_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE content_project_item ADD CONSTRAINT FK_2202E1B984715E3B FOREIGN KEY (project_item_id) REFERENCES project_item (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_project_item ADD CONSTRAINT FK_2202E1B984A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_item DROP FOREIGN KEY FK_268AED0684A0A3ED');
        $this->addSql('DROP INDEX IDX_268AED0684A0A3ED ON project_item');
        $this->addSql('ALTER TABLE project_item DROP content_id');
    }
}
