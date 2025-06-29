<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Completion implements \JsonSerializable
{
    public function __construct(
        public readonly array $values,
        public readonly int $total,
        public readonly bool $hasMore,
    ) {
    }

    public function jsonSerialize()
    {
        return [
            'values' => $this->values,
            'total' => $this->total,
            'hasMore' => $this->hasMore,
        ];
    }
}
