<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260608165638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, author VARCHAR(50) DEFAULT NULL, total_pages INT DEFAULT NULL, pages_read INT DEFAULT NULL, rating INT DEFAULT NULL, review LONGTEXT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, added_at DATETIME NOT NULL, is_favorite TINYINT NOT NULL, category VARCHAR(255) DEFAULT NULL, isbn VARCHAR(20) DEFAULT NULL, cover_url VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_CBE5A331A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT NOT NULL, firstname VARCHAR(50) DEFAULT NULL, lastname VARCHAR(50) DEFAULT NULL, pseudo VARCHAR(50) DEFAULT NULL, is_pseudo_displayed TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331A76ED395');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE `user`');
    }
}
