<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122143850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE establishment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE feedback_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prestataire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prestation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE slot_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE booking (id INT NOT NULL, booked_by_id INT DEFAULT NULL, prestation_id INT DEFAULT NULL, slot_id INT DEFAULT NULL, booking_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E00CEDDEF4A5BD90 ON booking (booked_by_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE9E45C554 ON booking (prestation_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE59E5119C ON booking (slot_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE establishment (id INT NOT NULL, relate_to_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DBEFB1EEE8BF6915 ON establishment (relate_to_id)');
        $this->addSql('CREATE TABLE feedback (id INT NOT NULL, issued_by_id INT DEFAULT NULL, prestation_id INT DEFAULT NULL, comment TEXT DEFAULT NULL, rating INT DEFAULT NULL, feedback_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D2294458784BB717 ON feedback (issued_by_id)');
        $this->addSql('CREATE INDEX IDX_D22944589E45C554 ON feedback (prestation_id)');
        $this->addSql('CREATE TABLE prestataire (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, contact_infos VARCHAR(255) DEFAULT NULL, sector VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prestation (id INT NOT NULL, establishment_id INT DEFAULT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, duration INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51C88FAD8565851 ON prestation (establishment_id)');
        $this->addSql('CREATE INDEX IDX_51C88FAD12469DE2 ON prestation (category_id)');
        $this->addSql('CREATE TABLE slot (id INT NOT NULL, prestation_id INT DEFAULT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC0E20679E45C554 ON slot (prestation_id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEF4A5BD90 FOREIGN KEY (booked_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE9E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE59E5119C FOREIGN KEY (slot_id) REFERENCES slot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE establishment ADD CONSTRAINT FK_DBEFB1EEE8BF6915 FOREIGN KEY (relate_to_id) REFERENCES prestataire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458784BB717 FOREIGN KEY (issued_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944589E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prestation ADD CONSTRAINT FK_51C88FAD8565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prestation ADD CONSTRAINT FK_51C88FAD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E20679E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD establishment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6498565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6498565851 ON "user" (establishment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6498565851');
        $this->addSql('DROP SEQUENCE booking_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE establishment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE feedback_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prestataire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prestation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE slot_id_seq CASCADE');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDEF4A5BD90');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE9E45C554');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE59E5119C');
        $this->addSql('ALTER TABLE establishment DROP CONSTRAINT FK_DBEFB1EEE8BF6915');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D2294458784BB717');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D22944589E45C554');
        $this->addSql('ALTER TABLE prestation DROP CONSTRAINT FK_51C88FAD8565851');
        $this->addSql('ALTER TABLE prestation DROP CONSTRAINT FK_51C88FAD12469DE2');
        $this->addSql('ALTER TABLE slot DROP CONSTRAINT FK_AC0E20679E45C554');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE establishment');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE prestataire');
        $this->addSql('DROP TABLE prestation');
        $this->addSql('DROP TABLE slot');
        $this->addSql('DROP INDEX IDX_8D93D6498565851');
        $this->addSql('ALTER TABLE "user" DROP establishment_id');
    }
}
