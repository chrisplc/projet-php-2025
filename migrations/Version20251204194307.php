<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204194307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur ADD nom VARCHAR(255) NOT NULL, DROP id');
        $this->addSql('ALTER TABLE utilisateur ADD PRIMARY KEY (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD id INT AUTO_INCREMENT NOT NULL, DROP nom, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
