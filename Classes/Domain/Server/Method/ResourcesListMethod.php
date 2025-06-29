<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\Resources;
use SJS\Neos\MCP\Domain\Server\Method\ResourcesListMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class ResourcesListMethod
{
    public static function handle(Resources\ListRequest $resourcesListRequest, array $resources, ?string $nextCursor): string
    {
        $response = new Response($resourcesListRequest->id);
        return $response->result(new Result($resources, $nextCursor));
    }
}
