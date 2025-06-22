<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method;


use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\Client\Request\ResourcesTemplatesListRequest;
use SJS\Neos\MCP\Domain\Server\Method\ResourcesTemplatesListMethod\Result;
use SJS\Neos\MCP\Transport\JsonRPC\Response;


#[Flow\Proxy(false)]
class ResourcesTemplatesListMethod
{
    public static function handle(ResourcesTemplatesListRequest $resourcesTemplatesListRequest, array $templates): string
    {
        $response = new Response($resourcesTemplatesListRequest->id);
        return $response->result(new Result($templates));
    }
}