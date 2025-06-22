<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method;


use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Server\Server;

abstract class AbstractMethod
{
    public function __construct(
        public readonly Server $server
    ) {
    }
}