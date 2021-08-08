<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804011813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clothe_user_profile (clothe_id INT NOT NULL, user_profile_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_5E6C07EBD554487F (clothe_id), INDEX IDX_5E6C07EB6B9DD454 (user_profile_id), PRIMARY KEY(clothe_id, user_profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profile (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clothe_user_profile ADD CONSTRAINT FK_5E6C07EBD554487F FOREIGN KEY (clothe_id) REFERENCES clothe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothe_user_profile ADD CONSTRAINT FK_5E6C07EB6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD user_profile_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496B9DD454 ON user (user_profile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothe_user_profile DROP FOREIGN KEY FK_5E6C07EB6B9DD454');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496B9DD454');
        $this->addSql('DROP TABLE clothe_user_profile');
        $this->addSql('DROP TABLE user_profile');
        $this->addSql('DROP INDEX UNIQ_8D93D6496B9DD454 ON user');
        $this->addSql('ALTER TABLE user DROP user_profile_id');
    }
}
