<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106133628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_post_item (id INT AUTO_INCREMENT NOT NULL, blog_post_id INT NOT NULL, content_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_7330AA5AA77FBEAF (blog_post_id), INDEX IDX_7330AA5A84A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post_item ADD CONSTRAINT FK_7330AA5AA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_item ADD CONSTRAINT FK_7330AA5A84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post_item DROP FOREIGN KEY FK_7330AA5AA77FBEAF');
        $this->addSql('ALTER TABLE blog_post_item DROP FOREIGN KEY FK_7330AA5A84A0A3ED');
        $this->addSql('DROP TABLE blog_post_item');
    }
}
