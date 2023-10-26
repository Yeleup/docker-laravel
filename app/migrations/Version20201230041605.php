<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201230041605 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A34C3A3BB');
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A3C54C8C93');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A34C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A3C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A3C54C8C93');
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A34C3A3BB');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A3C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A34C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
    }
}
