<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102181939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_vente (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E12B5C3E9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_vente ADD CONSTRAINT FK_E12B5C3E9D86650F FOREIGN KEY (user_id_id) REFERENCES vente (id)');
        $this->addSql('DROP TABLE compteur');
        $this->addSql('DROP TABLE lmedia');
        $this->addSql('ALTER TABLE document_user ADD libelle VARCHAR(255) NOT NULL, ADD commentaire VARCHAR(1000) DEFAULT NULL, ADD date_ajout DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compteur (id INT AUTO_INCREMENT NOT NULL, numprod INT NOT NULL, numl INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE lmedia (id INT AUTO_INCREMENT NOT NULL, numprod VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lien VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lig VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE document_vente DROP FOREIGN KEY FK_E12B5C3E9D86650F');
        $this->addSql('DROP TABLE document_vente');
        $this->addSql('ALTER TABLE document_user DROP libelle, DROP commentaire, DROP date_ajout');
    }
}
