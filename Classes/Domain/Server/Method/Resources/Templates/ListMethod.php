<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Resources\Templates;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\Resources;
use SJS\Neos\MCP\Domain\Server\Method\Resources\Templates\ListMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

#[Flow\Proxy(false)]
class ListMethod
{
    public static function handle(Resources\Templates\ListRequest $resourcesTemplatesListRequest, array $templates): string
    {
        $response = new Response($resourcesTemplatesListRequest->id);
        return $response->result(new Result($templates));
    }
}
