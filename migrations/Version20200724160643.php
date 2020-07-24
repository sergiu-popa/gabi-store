<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200724160643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('CREATE INDEX merchandise_name_idx ON merchandise (name)');
        $this->addSql('ALTER TABLE merchandise_payment ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE provider_debt CHANGE payment_type payment_type SMALLINT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, executed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP INDEX merchandise_name_idx ON merchandise');
        $this->addSql('ALTER TABLE merchandise_payment DROP updated_at');
        $this->addSql('ALTER TABLE provider_debt CHANGE payment_type payment_type SMALLINT DEFAULT 2 NOT NULL');
    }
}
