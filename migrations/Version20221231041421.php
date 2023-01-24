<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231041421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit_piece (produit_id INT NOT NULL, piece_id INT NOT NULL, INDEX IDX_74566CB7F347EFB (produit_id), INDEX IDX_74566CB7C40FCFA8 (piece_id), PRIMARY KEY(produit_id, piece_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_piece ADD CONSTRAINT FK_74566CB7F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_piece ADD CONSTRAINT FK_74566CB7C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_piece DROP FOREIGN KEY FK_74566CB7F347EFB');
        $this->addSql('ALTER TABLE produit_piece DROP FOREIGN KEY FK_74566CB7C40FCFA8');
        $this->addSql('DROP TABLE produit_piece');
    }
}
