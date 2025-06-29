<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Tools;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\Tools;
use SJS\Neos\MCP\Domain\Server\Method\Tools\CallMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class CallMethod
{
    public static function handle(Tools\CallRequest $toolsCallRequest, mixed $content): string
    {
        $response = new Response($toolsCallRequest->id);
        return $response->result(new Result($content));
    }
}
