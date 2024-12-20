<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220160921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE language (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reply (id SERIAL NOT NULL, author_id INT NOT NULL, topic_id INT NOT NULL, content TEXT NOT NULL, created_at DATE NOT NULL, updated_at DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FDA8C6E0F675F31B ON reply (author_id)');
        $this->addSql('CREATE INDEX IDX_FDA8C6E01F55203D ON reply (topic_id)');
        $this->addSql('CREATE TABLE topic (id SERIAL NOT NULL, author_id INT NOT NULL, category_id INT DEFAULT NULL, language_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9D40DE1BF675F31B ON topic (author_id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1B12469DE2 ON topic (category_id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1B82F1BAF4 ON topic (language_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, username VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E01F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reply DROP CONSTRAINT FK_FDA8C6E0F675F31B');
        $this->addSql('ALTER TABLE reply DROP CONSTRAINT FK_FDA8C6E01F55203D');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1BF675F31B');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1B12469DE2');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1B82F1BAF4');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE reply');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
