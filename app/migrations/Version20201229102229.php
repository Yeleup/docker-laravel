<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229102229 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD market_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09622F3F37 FOREIGN KEY (market_id) REFERENCES market (id)');
        $this->addSql('CREATE INDEX IDX_81398E09622F3F37 ON customer (market_id)');
        $this->addSql('ALTER TABLE market DROP FOREIGN KEY FK_6BAC85CBC3568B40');
        $this->addSql('DROP INDEX IDX_6BAC85CBC3568B40 ON market');
        $this->addSql('ALTER TABLE market DROP customers_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09622F3F37');
        $this->addSql('DROP INDEX IDX_81398E09622F3F37 ON customer');
        $this->addSql('ALTER TABLE customer DROP market_id');
        $this->addSql('ALTER TABLE market ADD customers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CBC3568B40 FOREIGN KEY (customers_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_6BAC85CBC3568B40 ON market (customers_id)');
    }
}
