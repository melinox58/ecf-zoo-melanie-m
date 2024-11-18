<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015091102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, id_animals_id INT NOT NULL, id_foods_id INT NOT NULL, id_users_id INT NOT NULL, date DATETIME NOT NULL, comment LONGTEXT NOT NULL, INDEX IDX_F11FA745B6A11E8F (id_animals_id), INDEX IDX_F11FA7452B19D99E (id_foods_id), INDEX IDX_F11FA745376858A8 (id_users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745B6A11E8F FOREIGN KEY (id_animals_id) REFERENCES animals (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7452B19D99E FOREIGN KEY (id_foods_id) REFERENCES foods (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745376858A8 FOREIGN KEY (id_users_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745B6A11E8F');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA7452B19D99E');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745376858A8');
        $this->addSql('DROP TABLE reports');
    }
}
