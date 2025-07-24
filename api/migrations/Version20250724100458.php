<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724100458 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create Mission database table and its relationship with Agent';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE TABLE mission (
          id INT AUTO_INCREMENT NOT NULL,
          name VARCHAR(255) NOT NULL,
          danger VARCHAR(255) NOT NULL,
          status VARCHAR(255) DEFAULT NULL,
          description VARCHAR(255) DEFAULT NULL,
          objectives VARCHAR(255) DEFAULT NULL,
          start_date DATE NOT NULL,
          end_date DATE DEFAULT NULL,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('CREATE TABLE mission_agent (
          mission_id INT NOT NULL,
          agent_id INT NOT NULL,
          INDEX IDX_B61DC3A0BE6CAE90 (mission_id),
          INDEX IDX_B61DC3A03414710B (agent_id),
          PRIMARY KEY(mission_id, agent_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE
          mission_agent
        ADD
          CONSTRAINT FK_B61DC3A0BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
		$this->addSql('ALTER TABLE
          mission_agent
        ADD
          CONSTRAINT FK_B61DC3A03414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE mission_agent DROP FOREIGN KEY FK_B61DC3A0BE6CAE90');
		$this->addSql('ALTER TABLE mission_agent DROP FOREIGN KEY FK_B61DC3A03414710B');
		$this->addSql('DROP TABLE mission');
		$this->addSql('DROP TABLE mission_agent');
	}
}
