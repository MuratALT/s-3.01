<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231045244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_piece DROP FOREIGN KEY FK_74566CB7F347EFB');
        $this->addSql('ALTER TABLE produit_piece DROP FOREIGN KEY FK_74566CB7C40FCFA8');
        $this->addSql('ALTER TABLE produit_piece ADD id INT AUTO_INCREMENT NOT NULL, ADD quantite INT NOT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE piece_id piece_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE produit_piece ADD CONSTRAINT FK_74566CB7F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_piece ADD CONSTRAINT FK_74566CB7C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_piece MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE produit_piece DROP FOREIGN KEY FK_74566CB7F347EFB');
        $this->addSql('ALTER TABLE produit_piece DROP FOREIGN KEY FK_74566CB7C40FCFA8');
        $this->addSql('DROP INDEX `PRIMARY` ON produit_piece');
        $this->addSql('ALTER TABLE produit_piece DROP id, DROP quantite, CHANGE produit_id produit_id INT NOT NULL, CHANGE piece_id piece_id INT NOT NULL');
        $this->addSql('ALTER TABLE produit_piece ADD CONSTRAINT FK_74566CB7F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_piece ADD CONSTRAINT FK_74566CB7C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_piece ADD PRIMARY KEY (produit_id, piece_id)');
    }
}
