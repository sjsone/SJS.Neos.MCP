<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Transport\JsonRPC\Request;

#[Flow\Proxy(false)]
class InitializeRequest
{
    public const string Method = "initialize";

    public function __construct(
        public readonly int $id
    ) {
    }

    public static function fromJsonRPCRequest(Request $request): self
    {
        return new self(
            $request->id,
        );
    }
}
