<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018105038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change price type to float';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product CHANGE price price DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product CHANGE price price DOUBLE PRECISION NOT NULL');
    }
}
