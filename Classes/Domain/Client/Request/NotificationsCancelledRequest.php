<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request;


use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Transport\JsonRPC\Request;


#[Flow\Proxy(false)]
class NotificationsCancelledRequest
{
    public const string Method = "notifications/cancelled";

    public function __construct()
    {
    }

    public static function fromJsonRPCRequest(Request $request): self
    {
        return new self();
    }
}