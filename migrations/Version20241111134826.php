<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111134826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animals ADD image_path VARCHAR(255) DEFAULT NULL, DROP counter');
        $this->addSql('ALTER TABLE images CHANGE id_services_id id_services_id INT NOT NULL, CHANGE id_animals_id id_animals_id INT NOT NULL, CHANGE id_habitats_id id_habitats_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animals ADD counter INT NOT NULL, DROP image_path');
        $this->addSql('ALTER TABLE images CHANGE id_services_id id_services_id INT DEFAULT NULL, CHANGE id_animals_id id_animals_id INT DEFAULT NULL, CHANGE id_habitats_id id_habitats_id INT DEFAULT NULL');
    }
}
