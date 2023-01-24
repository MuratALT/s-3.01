<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220230534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_produit (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_8DCA4AFBF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2A275ADAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fonction (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6AF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_marketing (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(1000) NOT NULL, fonctionnalites VARCHAR(1000) NOT NULL, benefices VARCHAR(1000) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_technique (id INT AUTO_INCREMENT NOT NULL, infoson_id INT DEFAULT NULL, infoalim_id INT DEFAULT NULL, duree_vie VARCHAR(255) NOT NULL, compatibilite VARCHAR(255) NOT NULL, hauteur DOUBLE PRECISION NOT NULL, largeur DOUBLE PRECISION NOT NULL, longueur DOUBLE PRECISION NOT NULL, profondeur DOUBLE PRECISION NOT NULL, poids DOUBLE PRECISION NOT NULL, INDEX IDX_7A4B257CE58E8B2B (infoson_id), INDEX IDX_7A4B257C3F1BC91B (infoalim_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE last_password (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, password1 VARCHAR(255) DEFAULT NULL, password2 VARCHAR(255) DEFAULT NULL, password3 VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4DA3034BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_produit (id INT AUTO_INCREMENT NOT NULL, typemedia_id INT NOT NULL, produit_id INT DEFAULT NULL, lien VARCHAR(255) NOT NULL, INDEX IDX_48726A6EB6F653FD (typemedia_id), UNIQUE INDEX UNIQ_48726A6EF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_4ED4099AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE piece (id INT AUTO_INCREMENT NOT NULL, infotech_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_44CA0B23509CC329 (infotech_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, infotech_id INT NOT NULL, infomarket_id INT NOT NULL, typeprod_id INT DEFAULT NULL, pu DOUBLE PRECISION NOT NULL, garantie VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, reference INT NOT NULL, UNIQUE INDEX UNIQ_29A5EC27509CC329 (infotech_id), UNIQUE INDEX UNIQ_29A5EC275BDA013D (infomarket_id), INDEX IDX_29A5EC27DB2CB3CE (typeprod_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit_reglementation (produit_id INT NOT NULL, reglementation_id INT NOT NULL, INDEX IDX_A8929AB6F347EFB (produit_id), INDEX IDX_A8929AB6D1AD45AF (reglementation_id), PRIMARY KEY(produit_id, reglementation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE puiss_son (id INT AUTO_INCREMENT NOT NULL, mesure DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reglementation (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, picto VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, user_id INT DEFAULT NULL, piece_id INT DEFAULT NULL, INDEX IDX_97A0ADA3F347EFB (produit_id), INDEX IDX_97A0ADA3A76ED395 (user_id), INDEX IDX_97A0ADA3C40FCFA8 (piece_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_alim (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_media (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_prod (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, fonction_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, is_freeze TINYINT(1) NOT NULL, date_naissance DATE NOT NULL, is_archive TINYINT(1) NOT NULL, usurp TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649ED5CA9E6 (service_id), INDEX IDX_8D93D64957889920 (fonction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, commentaire VARCHAR(255) NOT NULL, INDEX IDX_888A2A4CBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_produit ADD CONSTRAINT FK_8DCA4AFBF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE info_technique ADD CONSTRAINT FK_7A4B257CE58E8B2B FOREIGN KEY (infoson_id) REFERENCES puiss_son (id)');
        $this->addSql('ALTER TABLE info_technique ADD CONSTRAINT FK_7A4B257C3F1BC91B FOREIGN KEY (infoalim_id) REFERENCES type_alim (id)');
        $this->addSql('ALTER TABLE last_password ADD CONSTRAINT FK_4DA3034BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE media_produit ADD CONSTRAINT FK_48726A6EB6F653FD FOREIGN KEY (typemedia_id) REFERENCES type_media (id)');
        $this->addSql('ALTER TABLE media_produit ADD CONSTRAINT FK_48726A6EF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE media_user ADD CONSTRAINT FK_4ED4099AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B23509CC329 FOREIGN KEY (infotech_id) REFERENCES info_technique (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27509CC329 FOREIGN KEY (infotech_id) REFERENCES info_technique (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC275BDA013D FOREIGN KEY (infomarket_id) REFERENCES info_marketing (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DB2CB3CE FOREIGN KEY (typeprod_id) REFERENCES type_prod (id)');
        $this->addSql('ALTER TABLE produit_reglementation ADD CONSTRAINT FK_A8929AB6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_reglementation ADD CONSTRAINT FK_A8929AB6D1AD45AF FOREIGN KEY (reglementation_id) REFERENCES reglementation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64957889920 FOREIGN KEY (fonction_id) REFERENCES fonction (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_produit DROP FOREIGN KEY FK_8DCA4AFBF347EFB');
        $this->addSql('ALTER TABLE document_user DROP FOREIGN KEY FK_2A275ADAA76ED395');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AF347EFB');
        $this->addSql('ALTER TABLE info_technique DROP FOREIGN KEY FK_7A4B257CE58E8B2B');
        $this->addSql('ALTER TABLE info_technique DROP FOREIGN KEY FK_7A4B257C3F1BC91B');
        $this->addSql('ALTER TABLE last_password DROP FOREIGN KEY FK_4DA3034BA76ED395');
        $this->addSql('ALTER TABLE media_produit DROP FOREIGN KEY FK_48726A6EB6F653FD');
        $this->addSql('ALTER TABLE media_produit DROP FOREIGN KEY FK_48726A6EF347EFB');
        $this->addSql('ALTER TABLE media_user DROP FOREIGN KEY FK_4ED4099AA76ED395');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B23509CC329');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27509CC329');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC275BDA013D');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DB2CB3CE');
        $this->addSql('ALTER TABLE produit_reglementation DROP FOREIGN KEY FK_A8929AB6F347EFB');
        $this->addSql('ALTER TABLE produit_reglementation DROP FOREIGN KEY FK_A8929AB6D1AD45AF');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F347EFB');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3A76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C40FCFA8');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649ED5CA9E6');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64957889920');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CBCF5E72D');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE document_produit');
        $this->addSql('DROP TABLE document_user');
        $this->addSql('DROP TABLE fonction');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE info_marketing');
        $this->addSql('DROP TABLE info_technique');
        $this->addSql('DROP TABLE last_password');
        $this->addSql('DROP TABLE media_produit');
        $this->addSql('DROP TABLE media_user');
        $this->addSql('DROP TABLE piece');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_reglementation');
        $this->addSql('DROP TABLE puiss_son');
        $this->addSql('DROP TABLE reglementation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE type_alim');
        $this->addSql('DROP TABLE type_media');
        $this->addSql('DROP TABLE type_prod');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE vente');
    }
}
