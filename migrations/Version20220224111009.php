<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220224111009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE team_user');
        $this->addSql('ALTER TABLE contact_extrafield_value DROP FOREIGN KEY FK_6047ADAEC93CA2A8');
        $this->addSql('ALTER TABLE contact_extrafield_value ADD CONSTRAINT FK_6047ADAEC93CA2A8 FOREIGN KEY (contact_extrafield_id) REFERENCES contact_extrafields (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact CHANGE name name VARCHAR(50) DEFAULT \'Default Name\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone1 phone1 VARCHAR(20) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone2 phone2 VARCHAR(20) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(150) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE contact_extrafield_value DROP FOREIGN KEY FK_6047ADAEC93CA2A8');
        $this->addSql('ALTER TABLE contact_extrafield_value CHANGE value value VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE contact_extrafield_value ADD CONSTRAINT FK_6047ADAEC93CA2A8 FOREIGN KEY (contact_extrafield_id) REFERENCES contact_extrafield_value (id)');
        $this->addSql('ALTER TABLE contact_extrafields CHANGE input_type input_type VARCHAR(50) DEFAULT \'text\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE label label VARCHAR(50) DEFAULT \'Default Title\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE extra extra LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE contact_type CHANGE label label VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE color color VARCHAR(7) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE event CHANGE label label VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE color color VARCHAR(7) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE event_type CHANGE label label VARCHAR(50) DEFAULT \'No Title\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE color color VARCHAR(7) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE planning CHANGE label label VARCHAR(50) DEFAULT \'No Title\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE color color VARCHAR(7) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE team CHANGE color color VARCHAR(7) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE label label VARCHAR(50) DEFAULT \'No Title\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE last_name last_name VARCHAR(70) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(70) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(20) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE picture picture VARCHAR(200) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
