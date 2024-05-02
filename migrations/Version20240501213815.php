<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501213815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "passwords_resets" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, token VARCHAR(255) NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C56AE0CE7927C74 ON "passwords_resets" (email)');
        $this->addSql('COMMENT ON COLUMN "passwords_resets".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "passwords_resets".valid_until IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE "passwords_resets"');
    }
}
