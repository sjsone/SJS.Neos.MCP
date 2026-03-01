<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Command;

use Neos\Flow\Cli\CommandController;
use Neos\Flow\Annotations as Flow;

class MCPCommandController extends CommandController
{
    public function addAgentCommand(string $username): void
    {
        $this->outputLine("stub");
    }
}