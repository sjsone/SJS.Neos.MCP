<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Provider;

use Neos\Flow\Annotations as Flow;
use SJS\Flow\MCP\Domain\Connection\Connection;
use SJS\Flow\MCP\Domain\Connection\ConnectionProviderInterface;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;

class PersistentConnectionProvider implements ConnectionProviderInterface
{
    #[Flow\Inject]
    protected ConnectionDataRepository $connectionDataRepository;

    public function getConnectionByTokenAndServerName(string $token, string $serverName): ?Connection
    {
        $connectionData = $this->connectionDataRepository->findOneByToken($token);
        return $connectionData?->createConnection();
    }
}
