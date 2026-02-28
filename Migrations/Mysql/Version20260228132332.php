<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260228132332 extends AbstractMigration
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

        $this->addSql('CREATE TABLE sjs_neos_mcp_domain_model_agent (persistence_object_identifier VARCHAR(40) NOT NULL, party VARCHAR(40) DEFAULT NULL, name VARCHAR(255) NOT NULL, createdat DATETIME NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_58354A3189954EE0 (party), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_agent ADD CONSTRAINT FK_58354A3189954EE0 FOREIGN KEY (party) REFERENCES neos_party_domain_model_abstractparty (persistence_object_identifier)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1010Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1010Platform'."
        );

        $this->addSql('ALTER TABLE sjs_neos_mcp_domain_model_agent DROP FOREIGN KEY FK_58354A3189954EE0');
        $this->addSql('DROP TABLE sjs_neos_mcp_domain_model_agent');
    }
}
