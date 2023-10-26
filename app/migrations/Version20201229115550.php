<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229115550 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09622F3F37');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09622F3F37');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09622F3F37 FOREIGN KEY (market_id) REFERENCES market (id)');
    }
}
