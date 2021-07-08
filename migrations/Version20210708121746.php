<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210708121746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothe ADD created_by_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE clothe ADD CONSTRAINT FK_C32115BAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C32115BAB03A8386 ON clothe (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothe DROP FOREIGN KEY FK_C32115BAB03A8386');
        $this->addSql('DROP INDEX IDX_C32115BAB03A8386 ON clothe');
        $this->addSql('ALTER TABLE clothe DROP created_by_id, DROP created_at, DROP updated_at');
    }
}
