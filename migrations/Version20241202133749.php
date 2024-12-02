<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202133749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reports_vet (id INT AUTO_INCREMENT NOT NULL, id_animals_id INT NOT NULL, id_foods_id INT DEFAULT NULL, id_users_id INT NOT NULL, id_habitats_id INT DEFAULT NULL, date DATETIME NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_DAA9D133B6A11E8F (id_animals_id), INDEX IDX_DAA9D1332B19D99E (id_foods_id), INDEX IDX_DAA9D133376858A8 (id_users_id), INDEX IDX_DAA9D1332DC10B02 (id_habitats_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports_vet ADD CONSTRAINT FK_DAA9D133B6A11E8F FOREIGN KEY (id_animals_id) REFERENCES animals (id)');
        $this->addSql('ALTER TABLE reports_vet ADD CONSTRAINT FK_DAA9D1332B19D99E FOREIGN KEY (id_foods_id) REFERENCES foods (id)');
        $this->addSql('ALTER TABLE reports_vet ADD CONSTRAINT FK_DAA9D133376858A8 FOREIGN KEY (id_users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports_vet ADD CONSTRAINT FK_DAA9D1332DC10B02 FOREIGN KEY (id_habitats_id) REFERENCES habitats (id)');
        $this->addSql('ALTER TABLE animals ADD state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE habitats ADD state VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports_vet DROP FOREIGN KEY FK_DAA9D133B6A11E8F');
        $this->addSql('ALTER TABLE reports_vet DROP FOREIGN KEY FK_DAA9D1332B19D99E');
        $this->addSql('ALTER TABLE reports_vet DROP FOREIGN KEY FK_DAA9D133376858A8');
        $this->addSql('ALTER TABLE reports_vet DROP FOREIGN KEY FK_DAA9D1332DC10B02');
        $this->addSql('DROP TABLE reports_vet');
        $this->addSql('ALTER TABLE habitats DROP state');
        $this->addSql('ALTER TABLE animals DROP state');
    }
}
