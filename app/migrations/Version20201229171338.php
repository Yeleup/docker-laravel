<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229171338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer_order (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, amount NUMERIC(10, 0) NOT NULL, INDEX IDX_3B1CE6A3C54C8C93 (type_id), INDEX IDX_3B1CE6A34C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A3C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A34C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09622F3F37');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09622F3F37 FOREIGN KEY (market_id) REFERENCES market (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A34C3A3BB');
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A3C54C8C93');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE type');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09622F3F37');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON DELETE SET NULL');
    }
}
