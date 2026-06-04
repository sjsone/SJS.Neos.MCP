<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Provider;

use SJS\Flow\MCP\Domain\Provider\ConnectionProviderInterface;
use SJS\Flow\MCP\Domain\Model\Connection;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;
use Neos\Flow\Annotations as Flow;

class PersistentConnectionProvider implements ConnectionProviderInterface
{

    #[Flow\Inject]
    protected ConnectionDataRepository $connectionDataRepository;

    public function initialize(): void
    {
    }

    public function getConnectionByTokenAndServerName(string $token, string $serverName): ?Connection
    {
        $connectionData = $this->connectionDataRepository->findOneByToken($token);
        return $connectionData?->createConnection();
    }
}
