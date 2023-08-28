<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114140811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_item (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, UNIQUE INDEX UNIQ_98D7535FBE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pro_contact_job_item (pro_contact_id INT NOT NULL, job_item_id INT NOT NULL, INDEX IDX_BF05408B54D794EA (pro_contact_id), INDEX IDX_BF05408B8880E5C5 (job_item_id), PRIMARY KEY(pro_contact_id, job_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_item ADD CONSTRAINT FK_98D7535FBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE pro_contact_job_item ADD CONSTRAINT FK_BF05408B54D794EA FOREIGN KEY (pro_contact_id) REFERENCES pro_contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pro_contact_job_item ADD CONSTRAINT FK_BF05408B8880E5C5 FOREIGN KEY (job_item_id) REFERENCES job_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pro_contact_job DROP FOREIGN KEY FK_D0875499BE04EA9');
        $this->addSql('ALTER TABLE pro_contact_job DROP FOREIGN KEY FK_D087549954D794EA');
        $this->addSql('DROP TABLE pro_contact_job');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pro_contact_job (pro_contact_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_D087549954D794EA (pro_contact_id), INDEX IDX_D0875499BE04EA9 (job_id), PRIMARY KEY(pro_contact_id, job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pro_contact_job ADD CONSTRAINT FK_D0875499BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pro_contact_job ADD CONSTRAINT FK_D087549954D794EA FOREIGN KEY (pro_contact_id) REFERENCES pro_contact (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_item DROP FOREIGN KEY FK_98D7535FBE04EA9');
        $this->addSql('ALTER TABLE pro_contact_job_item DROP FOREIGN KEY FK_BF05408B54D794EA');
        $this->addSql('ALTER TABLE pro_contact_job_item DROP FOREIGN KEY FK_BF05408B8880E5C5');
        $this->addSql('DROP TABLE job_item');
        $this->addSql('DROP TABLE pro_contact_job_item');
    }
}
