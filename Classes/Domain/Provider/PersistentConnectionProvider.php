<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Provider;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use SJS\Flow\MCP\Domain\Connection\Connection;
use SJS\Flow\MCP\Domain\Connection\ConnectionProviderInterface;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;

class PersistentConnectionProvider implements ConnectionProviderInterface
{
    #[Flow\Inject]
    protected ConnectionDataRepository $connectionDataRepository;

    #[Flow\Inject]
    protected ConfigurationManager $configurationManager;

    /**
     * @return array<string>
     */
    protected function getAllowedSourceIdentifiers(): array
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
            'SJS.Neos.MCP'
        );
        return $settings['PersistentConnectionProvider']['allowedSourceIdentifiers'] ?? ['neos-backend'];
    }

    public function getConnectionByTokenAndServerName(string $token, string $serverName): ?Connection
    {
        $connectionData = $this->connectionDataRepository->findOneByToken($token);
        if ($connectionData === null) {
            return null;
        }

        // Only handle tokens from allowed source identifiers.
        $sourceIdentifier = $connectionData->getSourceIdentifier();
        $allowed = $this->getAllowedSourceIdentifiers();
        if (!\in_array($sourceIdentifier, $allowed, true)) {
            return null;
        }

        return $connectionData->createConnection();
    }
}
