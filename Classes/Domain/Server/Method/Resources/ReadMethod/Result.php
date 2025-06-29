<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Resources\ReadMethod;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Resource;

#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    /**
     * @param array<Resource> $resources
     */
    public function __construct(
        public readonly array $resources
    ) {
    }

    public function jsonSerialize()
    {
        return [
            'contents' => $this->resources
        ];
    }
}
