<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260611120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $hasAgentData = $schema->hasTable('sjs_neos_mcp_domain_model_agentdata');
        $hasIdentityData = $schema->hasTable('sjs_neos_mcp_domain_model_identitydata');
        $hasConnection = $schema->hasTable('sjs_neos_mcp_domain_model_connectiondata');

        if ($hasAgentData && !$hasConnection) {
            $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_agentdata TO sjs_neos_mcp_domain_model_connectiondata');
        } elseif ($hasIdentityData && !$hasConnection) {
            $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_identitydata TO sjs_neos_mcp_domain_model_connectiondata');
        }

        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_connectiondata ADD sourceidentifier VARCHAR(255) DEFAULT \'unknown\' NOT NULL');
        $this->addSql("UPDATE sjs_neos_mcp_domain_model_connectiondata SET sourceidentifier = 'neos-backend' WHERE sourceidentifier = 'unknown'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE sjs_neos_mcp_domain_model_connectiondata SET sourceidentifier = 'unknown' WHERE sourceidentifier = 'neos-backend'");
        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_connectiondata DROP sourceidentifier');
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_connectiondata TO sjs_neos_mcp_domain_model_agentdata');
    }
}
