<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724170617 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create message table and add foreign key to user table.';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE TABLE message (
          id INT AUTO_INCREMENT NOT NULL,
          send_by_id INT NOT NULL,
          title VARCHAR(255) NOT NULL,
          body VARCHAR(255) NOT NULL,
          INDEX IDX_B6BD307FC3852542 (send_by_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE
          message
        ADD
          CONSTRAINT FK_B6BD307FC3852542 FOREIGN KEY (send_by_id) REFERENCES user (id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FC3852542');
		$this->addSql('DROP TABLE message');
	}
}
