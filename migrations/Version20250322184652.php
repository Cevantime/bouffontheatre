<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250322184652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract ADD fetch_data_status VARCHAR(40) NOT NULL DEFAULT \'NOT_SENT\'');
        $this->addSql('UPDATE contract SET fetch_data_status = \'SENT_TO_COMPANY\' WHERE status = \'SENT_TO_COMPANY\'');
        $this->addSql('UPDATE contract SET fetch_data_status = \'FILLED_BY_COMPANY\' WHERE status = \'FILLED_BY_COMPANY\'');
        $this->addSql('UPDATE contract SET status = \'DRAFT\' WHERE status = \'FILLED_BY_COMPANY\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP fetch_data_status');
    }
}
