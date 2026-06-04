<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_agentdata TO sjs_neos_mcp_domain_model_connectiondata;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_connectiondata TO sjs_neos_mcp_domain_model_agentdata;');
    }
}
