<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20240903094947.php
final class Version20240903094947 extends AbstractMigration
========
final class Version20240903052804 extends AbstractMigration
>>>>>>>> 3b2dc7c91c0ed1f1cb2b2f38255dfbdb1f14f114:migrations/Version20240903052804.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20240903094947.php
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
========
        $this->addSql('ALTER TABLE project ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
>>>>>>>> 3b2dc7c91c0ed1f1cb2b2f38255dfbdb1f14f114:migrations/Version20240903052804.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20240903094947.php
        $this->addSql('DROP TABLE user');
========
        $this->addSql('ALTER TABLE project DROP created_at, DROP updated_at');
>>>>>>>> 3b2dc7c91c0ed1f1cb2b2f38255dfbdb1f14f114:migrations/Version20240903052804.php
    }
}
