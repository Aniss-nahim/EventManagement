<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523131314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, title VARCHAR(100) NOT NULL, type VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, state VARCHAR(60) NOT NULL, city VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, cover_image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3BAE0AA77E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_tag (id INT AUTO_INCREMENT NOT NULL, tag_id INT NOT NULL, tagged_event_id INT NOT NULL, INDEX IDX_12467250BAD26311 (tag_id), INDEX IDX_1246725025DAACC3 (tagged_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, participant_user_id INT NOT NULL, participated_event_id INT NOT NULL, type VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_AB55E24F3D631C9D (participant_user_id), INDEX IDX_AB55E24F85710596 (participated_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, critic_id INT NOT NULL, ciritic_subject_id INT NOT NULL, rating_score DOUBLE PRECISION NOT NULL, INDEX IDX_D8892622C7BE2830 (critic_id), INDEX IDX_D88926223B140259 (ciritic_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, tag_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B783B02CC1B0 (tag_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, birthdate DATE NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, registration_date DATETIME NOT NULL, image VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_tag ADD CONSTRAINT FK_12467250BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE event_tag ADD CONSTRAINT FK_1246725025DAACC3 FOREIGN KEY (tagged_event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F3D631C9D FOREIGN KEY (participant_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F85710596 FOREIGN KEY (participated_event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622C7BE2830 FOREIGN KEY (critic_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926223B140259 FOREIGN KEY (ciritic_subject_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_tag DROP FOREIGN KEY FK_1246725025DAACC3');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F85710596');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926223B140259');
        $this->addSql('ALTER TABLE event_tag DROP FOREIGN KEY FK_12467250BAD26311');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA77E3C61F9');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F3D631C9D');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622C7BE2830');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_tag');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
    }
}
