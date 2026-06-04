<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Provider;

use Neos\Flow\Annotations as Flow;
use SJS\Flow\MCP\Domain\Identity\Identity;
use SJS\Flow\MCP\Domain\Identity\IdentityProviderInterface;
use SJS\Neos\MCP\Domain\Repository\IdentityDataRepository;

class PersistentIdentityProvider implements IdentityProviderInterface
{
    #[Flow\Inject]
    protected IdentityDataRepository $identityDataRepository;

    public function getIdentityByTokenAndServerName(string $token, string $serverName): ?Identity
    {
        $identityData = $this->identityDataRepository->findOneByToken($token);
        return $identityData?->createIdentity();
    }
}
