<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Security\Authentication\Provider;

use Neos\Flow\Security\Authentication\AuthenticationProviderInterface;
use Neos\Flow\Security\Authentication\Provider\AbstractProvider;
use Neos\Flow\Security\Authentication\TokenInterface;
use SJS\Neos\MCP\Domain\Model\Agent;
use SJS\Neos\MCP\Domain\Repository\AgentRepository;
use SJS\Neos\MCP\Security\Authentication\Token\MCPToken;
use Neos\Flow\Annotations as Flow;


class MCPAuthenticationProvider extends AbstractProvider implements AuthenticationProviderInterface
{

    #[Flow\Inject]
    protected AgentRepository $agentRepository;

    public function getTokenClassNames()
    {
        return [MCPToken::class];
    }

    public function authenticate(TokenInterface $authenticationToken)
    {
        $token = $authenticationToken->getCredentials()["bearer"] ?? null;
        if ($token === null) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
            return;
        }

        /** @var Agent */
        $agent = $this->agentRepository->findOneByToken($token);
        if ($agent === null) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
            return;
        }

        // TODO: let user decide what account should be transferred to the Agent. This needs to happen in the Agent Backend Module
        foreach ($agent->getParty()->getAccounts() as $partyAccount) {
            $authenticationToken->setAccount($partyAccount);
            break;
        }

        $authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
    }

}