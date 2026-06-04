<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;
use SJS\Neos\MCP\Domain\Model\IdentityData;

/**
 * @Flow\Scope("singleton")
 * @extends Repository<IdentityData>
 */
class IdentityDataRepository extends Repository
{
    /**
     * @return \Neos\Flow\Persistence\QueryResultInterface<IdentityData>
     */
    public function findByParty(\Neos\Party\Domain\Model\AbstractParty $party): \Neos\Flow\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('party', $party));
        return $query->execute();
    }

    public function findOneByToken(string $token): ?IdentityData
    {
        $query = $this->createQuery();
        $query->matching($query->equals('token', $token));
        $result = $query->execute()->getFirst();

        if ($result !== null && !($result instanceof IdentityData)) {
            throw new \Exception("Query did not return null or IdentityData");
        }
        return $result;
    }
}
