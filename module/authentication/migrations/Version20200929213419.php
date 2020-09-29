<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\IrreversibleMigration;

final class Version20200929213419 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE failure_login_attempts (
                id UUID NOT NULL,
                ip VARCHAR(45) NOT NULL,
                username VARCHAR(255) DEFAULT NULL,
                data TEXT NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )'
        );
        $this->addSql('CREATE INDEX ip ON failure_login_attempts (ip)');
        $this->addSql('COMMENT ON COLUMN failure_login_attempts.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN failure_login_attempts.data IS \'(DC2Type:array)\'');
    }

    /**
     * @throws IrreversibleMigration
     */
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
