<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724102910 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add country_id to Mission table and establish relationship with Country';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE mission ADD country_id INT NOT NULL');
		$this->addSql('ALTER TABLE
          mission
        ADD
          CONSTRAINT FK_9067F23CF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
		$this->addSql('CREATE INDEX IDX_9067F23CF92F3E70 ON mission (country_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CF92F3E70');
		$this->addSql('DROP INDEX IDX_9067F23CF92F3E70 ON mission');
		$this->addSql('ALTER TABLE mission DROP country_id');
	}
}
