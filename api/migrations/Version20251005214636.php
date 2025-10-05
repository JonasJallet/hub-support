<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005214636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE support_case (id SERIAL NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, subject VARCHAR(255) NOT NULL, message TEXT NOT NULL, file BYTEA DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A01E0F57A76ED395 ON support_case (user_id)');
        $this->addSql('COMMENT ON COLUMN support_case.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C808BA5A ON users (last_name)');
        $this->addSql('COMMENT ON COLUMN users.last_login_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE support_case ADD CONSTRAINT FK_A01E0F57A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE support_case DROP CONSTRAINT FK_A01E0F57A76ED395');
        $this->addSql('DROP TABLE support_case');
        $this->addSql('DROP TABLE users');
    }
}
