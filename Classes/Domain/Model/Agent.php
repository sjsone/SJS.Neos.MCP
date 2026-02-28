<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Party\Domain\Model\AbstractParty;

/**
 * @Flow\Entity
 */
class Agent
{

    /**
     * @ORM\ManyToOne
     * @var AbstractParty
     */
    protected AbstractParty $party;

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
}
