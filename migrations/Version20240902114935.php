<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902114935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, ville_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_9D096811A73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, ville_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, rue VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude DOUBLE PRECISION NOT NULL, INDEX IDX_2F577D59A73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sortie (id INT AUTO_INCREMENT NOT NULL, organisateur_id INT DEFAULT NULL, lieu_id INT DEFAULT NULL, campus_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_sortie DATETIME NOT NULL, date_fin_inscription DATETIME NOT NULL, nombre_place INT NOT NULL, duree INT NOT NULL, description VARCHAR(255) NOT NULL, annulation TINYINT(1) NOT NULL, INDEX IDX_3C3FD3F2D936B2FA (organisateur_id), INDEX IDX_3C3FD3F26AB213CC (lieu_id), INDEX IDX_3C3FD3F2AF5D55E1 (campus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sortie_user (sortie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8A67684ACC72D953 (sortie_id), INDEX IDX_8A67684AA76ED395 (user_id), PRIMARY KEY(sortie_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, campus_id INT DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, photo LONGBLOB NOT NULL, `admin` TINYINT(1) NOT NULL, INDEX IDX_8D93D649AF5D55E1 (campus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, cp VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campus ADD CONSTRAINT FK_9D096811A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D936B2FA FOREIGN KEY (organisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F26AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE sortie_user ADD CONSTRAINT FK_8A67684ACC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sortie_user ADD CONSTRAINT FK_8A67684AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus DROP FOREIGN KEY FK_9D096811A73F0036');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A73F0036');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2D936B2FA');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F26AB213CC');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2AF5D55E1');
        $this->addSql('ALTER TABLE sortie_user DROP FOREIGN KEY FK_8A67684ACC72D953');
        $this->addSql('ALTER TABLE sortie_user DROP FOREIGN KEY FK_8A67684AA76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649AF5D55E1');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('DROP TABLE sortie_user');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
