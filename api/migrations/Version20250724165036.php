<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724165036 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add mentor relationship to Agent entity and ensure mission_result has a valid mission_id.';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE agent ADD mentor_id INT DEFAULT NULL');
		$this->addSql('ALTER TABLE
          agent
        ADD
          CONSTRAINT FK_268B9C9DDB403044 FOREIGN KEY (mentor_id) REFERENCES agent (id)');
		$this->addSql('CREATE INDEX IDX_268B9C9DDB403044 ON agent (mentor_id)');
		$this->addSql('ALTER TABLE mission_result CHANGE mission_id mission_id INT NOT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE mission_result CHANGE mission_id mission_id INT DEFAULT NULL');
		$this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DDB403044');
		$this->addSql('DROP INDEX IDX_268B9C9DDB403044 ON agent');
		$this->addSql('ALTER TABLE agent DROP mentor_id');
	}
}
