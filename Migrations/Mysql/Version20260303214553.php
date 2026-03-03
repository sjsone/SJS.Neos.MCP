<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303214553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_agent TO sjs_neos_mcp_domain_model_agentdata;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sjs_neos_mcp_domain_model_agentdata TO sjs_neos_mcp_domain_model_agent;');
    }
}
