<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Tools;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\Tools;
use SJS\Neos\MCP\Domain\Server\Method\Tools\ListMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class ListMethod
{
    public static function handle(Tools\ListRequest $toolsListRequest, array $tools, ?string $nextCursor): string
    {
        $response = new Response($toolsListRequest->id);
        return $response->result(new Result($tools, $nextCursor));
    }
}
