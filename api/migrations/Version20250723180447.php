<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723180447 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create Agent database table';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE TABLE agent (
					id INT AUTO_INCREMENT NOT NULL,
					user_id INT NOT NULL,
					codename VARCHAR(255) NOT NULL,
					years_of_experience INT NOT NULL,
					status VARCHAR(255) DEFAULT \'Available\' NOT NULL,
					enrolement_date DATE NOT NULL,
					UNIQUE INDEX UNIQ_268B9C9DA76ED395 (user_id),
					PRIMARY KEY(id)
				) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DA76ED395');
		$this->addSql('DROP TABLE agent');
	}
}
