<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301204324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1010Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1010Platform'."
        );

        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_agent ADD account VARCHAR(40) DEFAULT NULL, ADD onlyallowedroleidentifiers LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_agent ADD CONSTRAINT FK_58354A317D3656A4 FOREIGN KEY (account) REFERENCES neos_flow_security_account (persistence_object_identifier)');
        $this->addSql('CREATE INDEX IDX_58354A317D3656A4 ON sjs_neos_mcp_domain_model_agent (account)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1010Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1010Platform'."
        );

        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_agent DROP FOREIGN KEY FK_58354A317D3656A4');
        $this->addSql('DROP INDEX IDX_58354A317D3656A4 ON sjs_neos_mcp_domain_model_agent');
        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_agent DROP account, DROP onlyallowedroleidentifiers');
    }
}
