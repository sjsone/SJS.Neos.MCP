<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Completion;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\Server\Method\Completion\CompleteMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class CompleteMethod
{
    public static function handle(Request\Completion\CompleteRequest $completionCompleteRequest, Completion $completion): string
    {
        $response = new Response($completionCompleteRequest->id);
        return $response->result(new Result($completion));
    }
}
