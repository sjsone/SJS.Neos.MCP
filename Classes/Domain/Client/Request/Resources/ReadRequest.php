<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request\Resources;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Transport\JsonRPC\Request;

#[Flow\Proxy(false)]
class ReadRequest
{
    public const string Method = "resources/read";

    public function __construct(
        public readonly int $id,
        public readonly string $uri
    ) {
    }

    public static function fromJsonRPCRequest(Request $request): self
    {
        return new self(
            $request->id,
            $request->params['uri']
        );
    }
}
