<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method;


use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\CompletionCompleteRequest;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\Server\Method\CompletionCompleteMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;


#[Flow\Proxy(false)]
class CompletionCompleteMethod
{
    public static function handle(CompletionCompleteRequest $completionCompleteRequest, Completion $completion): string
    {
        $response = new Response($completionCompleteRequest->id);
        return $response->result(new Result($completion));
    }
}