<?php

declare(strict_types=1);

namespace CfdiAdmin\Account\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\IrreversibleMigration;

final class Version20200929204741 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE users (
                id UUID NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                locale VARCHAR(5) DEFAULT NULL,
                enabled BOOLEAN NOT NULL,
                locked BOOLEAN NOT NULL,
                expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                credentials_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                confirmation_token VARCHAR(180) DEFAULT NULL,
                password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                session_id VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');

        $this->addSql(
            'CREATE TABLE password_history (
                id UUID NOT NULL,
                user_id UUID NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )'
        );
        $this->addSql('CREATE INDEX IDX_F352144A76ED395 ON password_history (user_id)');
        $this->addSql('ALTER TABLE password_history
            ADD CONSTRAINT FK_F352144A76ED395 FOREIGN KEY (user_id)
            REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN password_history.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN password_history.user_id IS \'(DC2Type:uuid)\'');
    }

    /**
     * @throws IrreversibleMigration
     */
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
