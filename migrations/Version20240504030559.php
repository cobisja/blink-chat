<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240504030559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "chats" (id UUID NOT NULL, group_admin_id UUID DEFAULT NULL, channel VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_message VARCHAR(50) DEFAULT NULL, group_chat BOOLEAN DEFAULT false NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D68180FA2F98E47 ON "chats" (channel)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D68180F5E237E06 ON "chats" (name)');
        $this->addSql('CREATE INDEX IDX_2D68180F6D1BCE6F ON "chats" (group_admin_id)');
        $this->addSql('COMMENT ON COLUMN "chats".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats".group_admin_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "chats".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE chats_participants (chat_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(chat_id, user_id))');
        $this->addSql('CREATE INDEX IDX_56E2A3111A9A7125 ON chats_participants (chat_id)');
        $this->addSql('CREATE INDEX IDX_56E2A311A76ED395 ON chats_participants (user_id)');
        $this->addSql('COMMENT ON COLUMN chats_participants.chat_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN chats_participants.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "chats_archived_messages" (id UUID NOT NULL, message_id UUID NOT NULL, archived_by_id UUID NOT NULL, archived_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D4FAFAB4537A1329 ON "chats_archived_messages" (message_id)');
        $this->addSql('CREATE INDEX IDX_D4FAFAB477BE2925 ON "chats_archived_messages" (archived_by_id)');
        $this->addSql('COMMENT ON COLUMN "chats_archived_messages".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_archived_messages".message_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_archived_messages".archived_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_archived_messages".archived_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "chats_blocked_users" (id UUID NOT NULL, blocker_user_id UUID NOT NULL, blocked_user_id UUID NOT NULL, blocked_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D754BD5CC1668098 ON "chats_blocked_users" (blocker_user_id)');
        $this->addSql('CREATE INDEX IDX_D754BD5C1EBCBB63 ON "chats_blocked_users" (blocked_user_id)');
        $this->addSql('COMMENT ON COLUMN "chats_blocked_users".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_blocked_users".blocker_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_blocked_users".blocked_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_blocked_users".blocked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "chats_messages" (id UUID NOT NULL, sender_id UUID DEFAULT NULL, recipient_id UUID DEFAULT NULL, chat_id UUID NOT NULL, message_text TEXT NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, read_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_864EAAD3F624B39D ON "chats_messages" (sender_id)');
        $this->addSql('CREATE INDEX IDX_864EAAD3E92F8F78 ON "chats_messages" (recipient_id)');
        $this->addSql('CREATE INDEX IDX_864EAAD31A9A7125 ON "chats_messages" (chat_id)');
        $this->addSql('COMMENT ON COLUMN "chats_messages".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_messages".sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_messages".recipient_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_messages".chat_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "chats_messages".sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "chats_messages".read_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "chats" ADD CONSTRAINT FK_2D68180F6D1BCE6F FOREIGN KEY (group_admin_id) REFERENCES "users" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chats_participants ADD CONSTRAINT FK_56E2A3111A9A7125 FOREIGN KEY (chat_id) REFERENCES "chats" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chats_participants ADD CONSTRAINT FK_56E2A311A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_archived_messages" ADD CONSTRAINT FK_D4FAFAB4537A1329 FOREIGN KEY (message_id) REFERENCES "chats_messages" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_archived_messages" ADD CONSTRAINT FK_D4FAFAB477BE2925 FOREIGN KEY (archived_by_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_blocked_users" ADD CONSTRAINT FK_D754BD5CC1668098 FOREIGN KEY (blocker_user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_blocked_users" ADD CONSTRAINT FK_D754BD5C1EBCBB63 FOREIGN KEY (blocked_user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_messages" ADD CONSTRAINT FK_864EAAD3F624B39D FOREIGN KEY (sender_id) REFERENCES "users" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_messages" ADD CONSTRAINT FK_864EAAD3E92F8F78 FOREIGN KEY (recipient_id) REFERENCES "users" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "chats_messages" ADD CONSTRAINT FK_864EAAD31A9A7125 FOREIGN KEY (chat_id) REFERENCES "chats" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "chats" DROP CONSTRAINT FK_2D68180F6D1BCE6F');
        $this->addSql('ALTER TABLE chats_participants DROP CONSTRAINT FK_56E2A3111A9A7125');
        $this->addSql('ALTER TABLE chats_participants DROP CONSTRAINT FK_56E2A311A76ED395');
        $this->addSql('ALTER TABLE "chats_archived_messages" DROP CONSTRAINT FK_D4FAFAB4537A1329');
        $this->addSql('ALTER TABLE "chats_archived_messages" DROP CONSTRAINT FK_D4FAFAB477BE2925');
        $this->addSql('ALTER TABLE "chats_blocked_users" DROP CONSTRAINT FK_D754BD5CC1668098');
        $this->addSql('ALTER TABLE "chats_blocked_users" DROP CONSTRAINT FK_D754BD5C1EBCBB63');
        $this->addSql('ALTER TABLE "chats_messages" DROP CONSTRAINT FK_864EAAD3F624B39D');
        $this->addSql('ALTER TABLE "chats_messages" DROP CONSTRAINT FK_864EAAD3E92F8F78');
        $this->addSql('ALTER TABLE "chats_messages" DROP CONSTRAINT FK_864EAAD31A9A7125');
        $this->addSql('DROP TABLE "chats"');
        $this->addSql('DROP TABLE chats_participants');
        $this->addSql('DROP TABLE "chats_archived_messages"');
        $this->addSql('DROP TABLE "chats_blocked_users"');
        $this->addSql('DROP TABLE "chats_messages"');
    }
}
