<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125110926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes ADD registered_at VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE coupons CHANGE registered_at registered_at VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE produits CHANGE registered_at registered_at VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes DROP registered_at');
        $this->addSql('ALTER TABLE coupons CHANGE registered_at registered_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE produits CHANGE registered_at registered_at DATE DEFAULT NULL');
    }
}
