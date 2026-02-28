<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\TestFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;

class ServerInfoTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            name: 'server_info',
            description: 'Returns diagnostic information about the server environment (PHP version, Flow context, request info)',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'Server Info',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $httpRequest = $actionRequest->getHttpRequest();

        $info = [
            'phpVersion' => PHP_VERSION,
            'phpSapi' => PHP_SAPI,
            'flowContext' => getenv('FLOW_CONTEXT') ?: 'Development',
            'serverSoftware' => $httpRequest->getServerParams()['SERVER_SOFTWARE'] ?? 'unknown',
            'requestMethod' => $httpRequest->getMethod(),
            'requestHost' => (string) $httpRequest->getUri()->getHost(),
            'requestScheme' => $httpRequest->getUri()->getScheme(),
            'memoryUsage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'memoryPeak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];

        return Content::structured($info)->addText(json_encode($info, JSON_PRETTY_PRINT));
    }
}
