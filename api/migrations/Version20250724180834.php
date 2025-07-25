<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724180834 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add recipient relationship and creation timestamp to Message entity for mission notifications';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE
          message
        ADD
          recipient_id INT DEFAULT NULL,
        ADD
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
        CHANGE
          send_by_id send_by_id INT DEFAULT NULL');
		$this->addSql('ALTER TABLE
          message
        ADD
          CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
		$this->addSql('CREATE INDEX IDX_B6BD307FE92F8F78 ON message (recipient_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
		$this->addSql('DROP INDEX IDX_B6BD307FE92F8F78 ON message');
		$this->addSql('ALTER TABLE
          message
        DROP
          recipient_id,
        DROP
          created_at,
        CHANGE
          send_by_id send_by_id INT NOT NULL');
	}
}
