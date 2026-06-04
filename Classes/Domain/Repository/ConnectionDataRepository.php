<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;
use Neos\Party\Domain\Model\AbstractParty;
use SJS\Neos\MCP\Domain\Model\ConnectionData;

#[Flow\Scope("singleton")]
class ConnectionDataRepository extends Repository
{

    public function findByParty(AbstractParty $party): ?\Neos\Flow\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching($query->equals("party", $party))->execute();
    }

    public function findOneByToken(string $token): ?ConnectionData
    {
        $query = $this->createQuery();
        $result = $query->matching($query->equals("token", $token))->execute()->getFirst();
        if ($result !== null && !($result instanceof ConnectionData)) {
            throw new \Exception("Query did not return null or ConnectionData");
        }
        return $result;
    }
}
