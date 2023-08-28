<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203133325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_item ADD featuring_show_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media_item ADD CONSTRAINT FK_DC5CFACD249481E0 FOREIGN KEY (featuring_show_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_DC5CFACD249481E0 ON media_item (featuring_show_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_item DROP FOREIGN KEY FK_DC5CFACD249481E0');
        $this->addSql('DROP INDEX IDX_DC5CFACD249481E0 ON media_item');
        $this->addSql('ALTER TABLE media_item DROP featuring_show_id');
    }
}
