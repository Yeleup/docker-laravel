<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210112052510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3B1CE6A3A76ED395 ON customer_order (user_id)');
        $this->addSql('ALTER TABLE market DROP FOREIGN KEY FK_6BAC85CBA76ED395');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A3A76ED395');
        $this->addSql('DROP INDEX IDX_3B1CE6A3A76ED395 ON customer_order');
        $this->addSql('ALTER TABLE customer_order DROP user_id');
        $this->addSql('ALTER TABLE market DROP FOREIGN KEY FK_6BAC85CBA76ED395');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
