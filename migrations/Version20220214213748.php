<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220214213748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, user_create_id INT DEFAULT NULL, type LONGTEXT DEFAULT NULL, is_company TINYINT(1) DEFAULT 0 NOT NULL, name VARCHAR(50) DEFAULT \'Default Name\' NOT NULL, phone1 VARCHAR(20) DEFAULT NULL, phone2 VARCHAR(20) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, created_at DATETIME DEFAULT \'2022-01-01 00:00:00\' NOT NULL, INDEX IDX_4C62E638EEFE5067 (user_create_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_contact_extrafields (contact_id INT NOT NULL, contact_extrafields_id INT NOT NULL, INDEX IDX_3D77136FE7A1254A (contact_id), INDEX IDX_3D77136F3FB5AA5D (contact_extrafields_id), PRIMARY KEY(contact_id, contact_extrafields_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_extrafields (id INT AUTO_INCREMENT NOT NULL, input_type VARCHAR(50) DEFAULT \'text\' NOT NULL, label VARCHAR(50) DEFAULT \'Default Title\' NOT NULL, for_company TINYINT(1) DEFAULT 1 NOT NULL, extra LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, event_type_id INT NOT NULL, planning_id INT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME DEFAULT NULL, label VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, is_important TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_3BAE0AA7401B253C (event_type_id), INDEX IDX_3BAE0AA73D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_contact (event_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_C9354F9771F7E88B (event_id), INDEX IDX_C9354F97E7A1254A (contact_id), PRIMARY KEY(event_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) DEFAULT \'No Title\' NOT NULL, color VARCHAR(7) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, planning_owner_id INT DEFAULT NULL, label VARCHAR(50) DEFAULT \'No Title\' NOT NULL, color VARCHAR(7) DEFAULT NULL, UNIQUE INDEX UNIQ_D499BFF649393754 (planning_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, team_owner_id INT DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, label VARCHAR(50) DEFAULT \'No Title\' NOT NULL, description LONGTEXT DEFAULT NULL, is_private TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_C4E0A61FC67EBD87 (team_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(70) NOT NULL, first_name VARCHAR(70) NOT NULL, password VARCHAR(100) NOT NULL, roles LONGTEXT NOT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, picture VARCHAR(200) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_planning (user_id INT NOT NULL, planning_id INT NOT NULL, INDEX IDX_34145BF6A76ED395 (user_id), INDEX IDX_34145BF63D865311 (planning_id), PRIMARY KEY(user_id, planning_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_event (user_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_D96CF1FFA76ED395 (user_id), INDEX IDX_D96CF1FF71F7E88B (event_id), PRIMARY KEY(user_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638EEFE5067 FOREIGN KEY (user_create_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE contact_contact_extrafields ADD CONSTRAINT FK_3D77136FE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_contact_extrafields ADD CONSTRAINT FK_3D77136F3FB5AA5D FOREIGN KEY (contact_extrafields_id) REFERENCES contact_extrafields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA73D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE event_contact ADD CONSTRAINT FK_C9354F9771F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_contact ADD CONSTRAINT FK_C9354F97E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF649393754 FOREIGN KEY (planning_owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FC67EBD87 FOREIGN KEY (team_owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_planning ADD CONSTRAINT FK_34145BF6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_planning ADD CONSTRAINT FK_34145BF63D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FF71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_contact_extrafields DROP FOREIGN KEY FK_3D77136FE7A1254A');
        $this->addSql('ALTER TABLE event_contact DROP FOREIGN KEY FK_C9354F97E7A1254A');
        $this->addSql('ALTER TABLE contact_contact_extrafields DROP FOREIGN KEY FK_3D77136F3FB5AA5D');
        $this->addSql('ALTER TABLE event_contact DROP FOREIGN KEY FK_C9354F9771F7E88B');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FF71F7E88B');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7401B253C');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA73D865311');
        $this->addSql('ALTER TABLE user_planning DROP FOREIGN KEY FK_34145BF63D865311');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638EEFE5067');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF649393754');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FC67EBD87');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('ALTER TABLE user_planning DROP FOREIGN KEY FK_34145BF6A76ED395');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FFA76ED395');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contact_contact_extrafields');
        $this->addSql('DROP TABLE contact_extrafields');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_contact');
        $this->addSql('DROP TABLE event_type');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_planning');
        $this->addSql('DROP TABLE user_event');
    }
}
