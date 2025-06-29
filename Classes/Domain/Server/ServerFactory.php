<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\ObjectManagement\ObjectManager;
use Psr\Log\LoggerInterface;

#[Flow\Scope('singleton')]
class ServerFactory
{
    #[Flow\InjectConfiguration(path: 'server')]
    protected array $configuration;

    #[Flow\Inject]
    protected ObjectManager $objectManager;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    public function buildFromName(string $name, ActionRequest $actionRequest): ?Server
    {
        return new Server(
            $name,
            $this->configuration[$name],
            $actionRequest,
            $this->objectManager,
            $this->logger
        );
    }
}
