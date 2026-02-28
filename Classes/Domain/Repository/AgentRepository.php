<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;
use Neos\Party\Domain\Model\AbstractParty;
use SJS\Neos\MCP\Domain\Model\Agent;

#[Flow\Scope("singleton")]
class AgentRepository extends Repository
{

    public function findByParty(AbstractParty $party): ?\Neos\Flow\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching($query->equals("party", $party))->execute();
    }

    public function findOneByToken(string $token): ?Agent
    {
        $query = $this->createQuery();
        return $query->matching($query->equals("token", $token))->execute()->getFirst();
    }
}