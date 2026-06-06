<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260606120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sourceIdentifier column to ConnectionData table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_connectiondata
            ADD sourceidentifier VARCHAR(255) DEFAULT \'unknown\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_connectiondata
            DROP sourceidentifier');
    }
}
