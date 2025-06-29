<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Resources;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\Resources;
use SJS\Neos\MCP\Domain\Server\Method\Resources\ListMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class ListMethod
{
    public static function handle(Resources\ListRequest $resourcesListRequest, array $resources, ?string $nextCursor): string
    {
        $response = new Response($resourcesListRequest->id);
        return $response->result(new Result($resources, $nextCursor));
    }
}
