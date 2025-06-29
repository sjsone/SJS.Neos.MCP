<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request\Tools;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Transport\JsonRPC\Request;

#[Flow\Proxy(false)]
class CallRequest
{
    public const string Method = "tools/call";

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $arguments,
    ) {
    }

    public static function fromJsonRPCRequest(Request $request): self
    {
        return new self(
            $request->id,
            $request->params['name'],
            $request->params['arguments'],
        );
    }
}
