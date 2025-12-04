<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204201225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, utilisateur_email VARCHAR(255) NOT NULL, spectacle_id INT NOT NULL, nombre_places INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, date_reservation DATETIME NOT NULL, INDEX IDX_42C84955215724AA (utilisateur_email), INDEX IDX_42C84955C682915D (spectacle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE spectacle (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, prix NUMERIC(10, 2) NOT NULL, lieu VARCHAR(255) NOT NULL, image VARCHAR(500) DEFAULT NULL, places_disponibles INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955215724AA FOREIGN KEY (utilisateur_email) REFERENCES utilisateur (email)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C682915D FOREIGN KEY (spectacle_id) REFERENCES spectacle (id)');
        $this->addSql('DROP INDEX idx_email ON utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955215724AA');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C682915D');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE spectacle');
        $this->addSql('CREATE INDEX idx_email ON utilisateur (email)');
    }
}
