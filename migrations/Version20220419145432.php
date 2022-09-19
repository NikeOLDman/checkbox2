<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220419145432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__tasks AS SELECT id, title, description, createtime, deadline, checked, deleted FROM tasks');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('CREATE TABLE tasks (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, title CLOB NOT NULL, description CLOB DEFAULT NULL, createtime DATETIME NOT NULL, deadline DATETIME NOT NULL, checked DATETIME DEFAULT NULL, deleted BOOLEAN NOT NULL, CONSTRAINT FK_50586597A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO tasks (id, title, description, createtime, deadline, checked, deleted) SELECT id, title, description, createtime, deadline, checked, deleted FROM __temp__tasks');
        $this->addSql('DROP TABLE __temp__tasks');
        $this->addSql('CREATE INDEX IDX_50586597A76ED395 ON tasks (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_50586597A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tasks AS SELECT id, title, description, createtime, deadline, checked, deleted FROM tasks');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('CREATE TABLE tasks (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title CLOB NOT NULL, description CLOB DEFAULT NULL, createtime DATETIME NOT NULL, deadline DATETIME NOT NULL, checked DATETIME DEFAULT NULL, deleted BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO tasks (id, title, description, createtime, deadline, checked, deleted) SELECT id, title, description, createtime, deadline, checked, deleted FROM __temp__tasks');
        $this->addSql('DROP TABLE __temp__tasks');
    }
}
