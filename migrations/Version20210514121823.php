<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210514121823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, participant_user_id INT NOT NULL, participated_event_id INT NOT NULL, type VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_AB55E24F3D631C9D (participant_user_id), INDEX IDX_AB55E24F85710596 (participated_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, critic_id INT NOT NULL, ciritic_subject_id INT NOT NULL, rating_score DOUBLE PRECISION NOT NULL, INDEX IDX_D8892622C7BE2830 (critic_id), INDEX IDX_D88926223B140259 (ciritic_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F3D631C9D FOREIGN KEY (participant_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F85710596 FOREIGN KEY (participated_event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622C7BE2830 FOREIGN KEY (critic_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926223B140259 FOREIGN KEY (ciritic_subject_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE rating');
    }
}
