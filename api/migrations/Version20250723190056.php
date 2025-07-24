<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723190056 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create country and rework agent tables';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE TABLE country (
          id INT AUTO_INCREMENT NOT NULL,
          cell_leader_id INT DEFAULT NULL,
          name VARCHAR(255) NOT NULL,
          danger VARCHAR(255) NOT NULL,
          number_of_agents INT NOT NULL,
          UNIQUE INDEX UNIQ_5373C966984E18B1 (cell_leader_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE
          country
        ADD
          CONSTRAINT FK_5373C966984E18B1 FOREIGN KEY (cell_leader_id) REFERENCES agent (id)');
		$this->addSql('ALTER TABLE agent ADD country_infiltrated_id INT DEFAULT NULL');
		$this->addSql('ALTER TABLE
          agent
        ADD
          CONSTRAINT FK_268B9C9D3E2C6169 FOREIGN KEY (country_infiltrated_id) REFERENCES country (id)');
		$this->addSql('CREATE INDEX IDX_268B9C9D3E2C6169 ON agent (country_infiltrated_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9D3E2C6169');
		$this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966984E18B1');
		$this->addSql('DROP TABLE country');
		$this->addSql('DROP INDEX IDX_268B9C9D3E2C6169 ON agent');
		$this->addSql('ALTER TABLE agent DROP country_infiltrated_id');
	}
}
