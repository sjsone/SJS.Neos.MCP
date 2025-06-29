<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Ref
{
    public function __construct(
        // TODO: create enum for completion/complete request ref.type
        public readonly string $type,
        public readonly string $uri,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['type'],
            $data['uri'],
        );
    }
}
