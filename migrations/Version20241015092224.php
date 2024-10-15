<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015092224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, id_services_id INT NOT NULL, id_animals_id INT NOT NULL, id_habitats_id INT NOT NULL, name VARCHAR(40) NOT NULL, src LONGBLOB NOT NULL, INDEX IDX_E01FBE6AB6E76B36 (id_services_id), INDEX IDX_E01FBE6AB6A11E8F (id_animals_id), INDEX IDX_E01FBE6A2DC10B02 (id_habitats_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB6E76B36 FOREIGN KEY (id_services_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB6A11E8F FOREIGN KEY (id_animals_id) REFERENCES animals (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A2DC10B02 FOREIGN KEY (id_habitats_id) REFERENCES habitats (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AB6E76B36');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AB6A11E8F');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A2DC10B02');
        $this->addSql('DROP TABLE images');
    }
}
