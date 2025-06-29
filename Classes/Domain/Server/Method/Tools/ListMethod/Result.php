<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Tools\ListMethod;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Resource;

#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    /**
     * @param array<Resource> $tools
     */
    public function __construct(
        public readonly array $tools,
        public readonly ?string $nextCursor,
    ) {
    }

    public function jsonSerialize()
    {
        $data = [
            'tools' => $this->tools
        ];

        if ($this->nextCursor) {
            $data['nextCursor'] = $this->nextCursor;
        }
        return $data;
    }
}
