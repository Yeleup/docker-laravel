<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210125111841 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE market_user (market_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C29F2FF9622F3F37 (market_id), INDEX IDX_C29F2FF9A76ED395 (user_id), PRIMARY KEY(market_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE market_user ADD CONSTRAINT FK_C29F2FF9622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE market_user ADD CONSTRAINT FK_C29F2FF9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE market DROP FOREIGN KEY FK_6BAC85CBA76ED395');
        $this->addSql('DROP INDEX IDX_6BAC85CBA76ED395 ON market');
        $this->addSql('ALTER TABLE market DROP user_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE market_user');
        $this->addSql('ALTER TABLE market ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6BAC85CBA76ED395 ON market (user_id)');
    }
}
