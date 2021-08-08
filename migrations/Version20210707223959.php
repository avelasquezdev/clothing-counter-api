<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707223959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_clothe (category_id INT NOT NULL, clothe_id INT NOT NULL, INDEX IDX_CCC9DFC112469DE2 (category_id), INDEX IDX_CCC9DFC1D554487F (clothe_id), PRIMARY KEY(category_id, clothe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clothe (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price VARCHAR(25) NOT NULL, popularity VARCHAR(25) NOT NULL, impacts INT NOT NULL, is_recommended TINYINT(1) NOT NULL, INDEX IDX_C32115BA3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_clothe ADD CONSTRAINT FK_CCC9DFC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_clothe ADD CONSTRAINT FK_CCC9DFC1D554487F FOREIGN KEY (clothe_id) REFERENCES clothe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothe ADD CONSTRAINT FK_C32115BA3DA5256D FOREIGN KEY (image_id) REFERENCES media_object (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_clothe DROP FOREIGN KEY FK_CCC9DFC112469DE2');
        $this->addSql('ALTER TABLE category_clothe DROP FOREIGN KEY FK_CCC9DFC1D554487F');
        $this->addSql('ALTER TABLE clothe DROP FOREIGN KEY FK_C32115BA3DA5256D');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_clothe');
        $this->addSql('DROP TABLE clothe');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP TABLE user');
    }
}
