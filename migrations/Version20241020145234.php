<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020145234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change city_id to nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` CHANGE city_id city_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` CHANGE city_id city_id INT NOT NULL');
    }
}
