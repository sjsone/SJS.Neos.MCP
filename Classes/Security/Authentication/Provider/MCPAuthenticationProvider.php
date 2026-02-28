<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Security\Authentication\Provider;

use Neos\Flow\Security\Authentication\AuthenticationProviderInterface;
use Neos\Flow\Security\Authentication\Provider\AbstractProvider;
use Neos\Flow\Security\Authentication\TokenInterface;
use SJS\Neos\MCP\Security\Authentication\Token\MCPToken;


class MCPAuthenticationProvider extends AbstractProvider implements AuthenticationProviderInterface
{
    public function getTokenClassNames()
    {
        return [MCPToken::class];
    }

    public function authenticate(TokenInterface $authenticationToken)
    {

    }

}