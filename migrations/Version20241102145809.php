<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102145809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD start_time DATETIME NOT NULL, ADD end_time DATETIME NOT NULL, ADD day_of_week INT NOT NULL, DROP date, DROP time');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP start_time, DROP end_time, DROP day_of_week');
    }
}
