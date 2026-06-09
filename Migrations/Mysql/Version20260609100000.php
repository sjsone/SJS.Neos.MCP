<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260609100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix sourceIdentifier default: unknown -> neos-backend for existing ConnectionData rows';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE sjs_neos_mcp_domain_model_connectiondata SET sourceidentifier = 'neos-backend' WHERE sourceidentifier = 'unknown'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE sjs_neos_mcp_domain_model_connectiondata SET sourceidentifier = 'unknown' WHERE sourceidentifier = 'neos-backend'");
    }
}
