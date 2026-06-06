<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rename the IdentityData table to ConnectionData — part of the Identity→Connection
 * rename across the SJS MCP packages. The ConnectionData entity (formerly IdentityData)
 * maps to this table, which previously held identity records.
 */
final class Version20260605135500 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_identitydata TO sjs_neos_mcp_domain_model_connectiondata');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_connectiondata TO sjs_neos_mcp_domain_model_identitydata');
    }
}
