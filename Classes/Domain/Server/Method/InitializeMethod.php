<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\InitializeRequest;
use SJS\Neos\MCP\Domain\Server\Method\InitializeMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class InitializeMethod
{
    public static function handle(InitializeRequest $initializeRequest): string
    {

        $response = new Response($initializeRequest->id);



        return $response->result(new Result());
    }
}
