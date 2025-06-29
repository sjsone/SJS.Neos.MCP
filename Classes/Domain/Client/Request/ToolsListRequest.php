<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Transport\JsonRPC\Request;

#[Flow\Proxy(false)]
class ToolsListRequest
{
    public const string Method = "tools/list";

    public function __construct(
        public readonly ?string $cursor = null,
    ) {
    }

    public static function fromJsonRPCRequest(Request $request): self
    {
        return new self(
            $request->params['cursor'] ?? null
        );
    }
}
