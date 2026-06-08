<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260608161457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, author VARCHAR(50) DEFAULT NULL, total_pages INT DEFAULT NULL, pages_read INT DEFAULT NULL, rating INT DEFAULT NULL, review LONGTEXT DEFAULT NULL, status LONGTEXT DEFAULT NULL, added_at DATETIME NOT NULL, is_favorite TINYINT NOT NULL, category LONGTEXT DEFAULT NULL, isbn VARCHAR(20) DEFAULT NULL, cover_url VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_CBE5A331A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331A76ED395');
        $this->addSql('DROP TABLE book');
    }
}
