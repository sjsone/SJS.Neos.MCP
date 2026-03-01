<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Security\Authentication\Provider;

use Neos\Flow\Security\Account;
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

        $account = $this->getAccountFromAgent($agent);
        if ($account === null) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
            return;
        }

        $authenticationToken->setAccount($account);

        $authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
    }

    protected function getAccountFromAgent(Agent $agent): ?Account
    {
        $account = $agent->getAccount();
        if ($account !== null) {
            return $account;
        }
        foreach ($agent->getParty()->getAccounts() as $partyAccount) {
            return $partyAccount;
        }

        return null;
    }
}