<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Resources;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\Resources;
use SJS\Neos\MCP\Domain\Server\Method\Resources\ReadMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class ReadMethod
{
    public static function handle(Resources\ReadRequest $resourcesListRequest, array $resources): string
    {
        $response = new Response($resourcesListRequest->id);
        return $response->result(new Result($resources));
    }
}
