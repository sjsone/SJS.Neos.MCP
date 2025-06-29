<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\ResourcesListMethod;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    public function __construct(
        public readonly array $resources,
        public readonly ?string $nextCursor,
    ) {
    }

    public function jsonSerialize()
    {
        $data = [
            "resources" => $this->resources,
        ];

        if ($this->nextCursor) {
            $data['nextCursor'] = $this->nextCursor;
        }

        return $data;
    }
}
