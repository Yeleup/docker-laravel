<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210125114937 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_market (user_id INT NOT NULL, market_id INT NOT NULL, INDEX IDX_EFE9EF6A76ED395 (user_id), INDEX IDX_EFE9EF6622F3F37 (market_id), PRIMARY KEY(user_id, market_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_market ADD CONSTRAINT FK_EFE9EF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_market ADD CONSTRAINT FK_EFE9EF6622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE market_user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE market_user (market_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C29F2FF9A76ED395 (user_id), INDEX IDX_C29F2FF9622F3F37 (market_id), PRIMARY KEY(market_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE market_user ADD CONSTRAINT FK_C29F2FF9622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE market_user ADD CONSTRAINT FK_C29F2FF9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user_market');
    }
}
