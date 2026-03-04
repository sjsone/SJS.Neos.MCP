<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Account;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Flow\Security\Policy\Role;
use SJS\Flow\MCP\Domain\Model\Agent;

/**
 * @Flow\Entity
 */
class AgentData
{
    /**
     * @ORM\ManyToOne
     * @var AbstractParty
     */
    protected AbstractParty $party;

    /**
     * @ORM\ManyToOne
     * @var Account
     */
    protected ?Account $account = null;

    /**
     * @var array<string> of strings
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $onlyAllowedRoleIdentifiers = [];

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var \DateTime
     */
    protected DateTime $createdAt;

    /**
     * @var string
     */
    protected string $token = '';

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getParty(): AbstractParty
    {
        return $this->party;
    }

    public function setParty(AbstractParty $party): self
    {
        $this->party = $party;
        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getOnlyAllowedRoleIdentifiers(): array
    {
        return $this->onlyAllowedRoleIdentifiers;
    }

    /**
     * @param array<string> $onlyAllowedRoleIdentifiers
     * @return AgentData
     */
    public function setOnlyAllowedRoleIdentifiers(array $onlyAllowedRoleIdentifiers): self
    {
        $this->onlyAllowedRoleIdentifiers = $onlyAllowedRoleIdentifiers;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function createAgent(): Agent
    {
        $account = $this->account;
        if ($account === null) {
            $account = $this->party->getAccounts()[0];
        }

        if (!($account instanceof Account)) {
            throw new \InvalidArgumentException("Agent requires account to be of type Account");
        }

        return Agent::create(
            name: $this->name,
            account: $account,
            token: $this->token
        );
    }
}
