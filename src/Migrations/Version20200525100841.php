<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525100841 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE provider_debt DROP FOREIGN KEY FK_3E3769FFCFC6D428');
        $this->addSql('DROP INDEX IDX_3E3769FFCFC6D428 ON provider_debt');
        $this->addSql('ALTER TABLE provider_debt DROP merchandise_id');
        $this->addSql('ALTER TABLE merchandise_payment DROP FOREIGN KEY FK_3AEA836CCFC6D428');
        $this->addSql('DROP INDEX IDX_3AEA836CCFC6D428 ON merchandise_payment');
        $this->addSql('ALTER TABLE merchandise_payment DROP merchandise_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE merchandise_payment ADD merchandise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE merchandise_payment ADD CONSTRAINT FK_3AEA836CCFC6D428 FOREIGN KEY (merchandise_id) REFERENCES merchandise (id)');
        $this->addSql('CREATE INDEX IDX_3AEA836CCFC6D428 ON merchandise_payment (merchandise_id)');
        $this->addSql('ALTER TABLE provider_debt ADD merchandise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE provider_debt ADD CONSTRAINT FK_3E3769FFCFC6D428 FOREIGN KEY (merchandise_id) REFERENCES merchandise (id)');
        $this->addSql('CREATE INDEX IDX_3E3769FFCFC6D428 ON provider_debt (merchandise_id)');
    }
}
