<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221226085639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_item DROP FOREIGN KEY FK_AE5CB7B7166D1F9C');
        $this->addSql('DROP INDEX IDX_AE5CB7B7166D1F9C ON link_item');
        $this->addSql('ALTER TABLE link_item CHANGE project_id shop_linked_show_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE link_item ADD CONSTRAINT FK_AE5CB7B72B28C5F5 FOREIGN KEY (shop_linked_show_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_AE5CB7B72B28C5F5 ON link_item (shop_linked_show_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_item DROP FOREIGN KEY FK_AE5CB7B72B28C5F5');
        $this->addSql('DROP INDEX IDX_AE5CB7B72B28C5F5 ON link_item');
        $this->addSql('ALTER TABLE link_item CHANGE shop_linked_show_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE link_item ADD CONSTRAINT FK_AE5CB7B7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AE5CB7B7166D1F9C ON link_item (project_id)');
    }
}
