<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505155007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        $this->addSql('ALTER TABLE users ADD query_field TEXT NOT NULL');
        $this->addSql("CREATE INDEX IDX_CA1C6581ABBEAC20 ON users using gin (query_field gin_trgm_ops)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_CA1C6581ABBEAC20');
        $this->addSql('ALTER TABLE "users" DROP query_field');
        $this->addSql('DROP EXTENSION IF EXISTS pg_trgm');
    }
}
