<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015090428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animals ADD id_habitats_id INT NOT NULL');
        $this->addSql('ALTER TABLE animals ADD CONSTRAINT FK_966C69DD2DC10B02 FOREIGN KEY (id_habitats_id) REFERENCES habitats (id)');
        $this->addSql('CREATE INDEX IDX_966C69DD2DC10B02 ON animals (id_habitats_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animals DROP FOREIGN KEY FK_966C69DD2DC10B02');
        $this->addSql('DROP INDEX IDX_966C69DD2DC10B02 ON animals');
        $this->addSql('ALTER TABLE animals DROP id_habitats_id');
    }
}
