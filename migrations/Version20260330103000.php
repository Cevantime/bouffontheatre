<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260330103000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Ajoute la table des heures supplémentaires liées au workflow';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE workflow_overtime (id INT AUTO_INCREMENT NOT NULL, workflow_id INT NOT NULL, worked_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', hour_count NUMERIC(5, 2) NOT NULL, unit_hour_price NUMERIC(10, 2) NOT NULL, INDEX IDX_WORKFLOW_OVERTIME_WORKFLOW (workflow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE workflow_overtime ADD CONSTRAINT FK_WORKFLOW_OVERTIME_WORKFLOW FOREIGN KEY (workflow_id) REFERENCES workflow (id) ON DELETE CASCADE');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('DROP TABLE workflow_overtime');
	}
}
