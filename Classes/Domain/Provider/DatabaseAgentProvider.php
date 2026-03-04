<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Provider;

use SJS\Flow\MCP\Domain\Provider\AgentProviderInterface;
use SJS\Flow\MCP\Domain\Model\Agent;
use SJS\Neos\MCP\Domain\Repository\AgentDataRepository;
use Neos\Flow\Annotations as Flow;

class DatabaseAgentProvider implements AgentProviderInterface
{

    #[Flow\Inject]
    protected AgentDataRepository $agentDataRepository;

    public function initialize(): void
    {
    }

    public function getAgentByToken(string $token): ?Agent
    {
        $agentData = $this->agentDataRepository->findOneByToken($token);
        return $agentData?->createAgent();
    }
}