<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\TestFeatureSet;

use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class PingTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            name: 'ping',
            description: 'Echoes back a message. Useful for testing connectivity to the MCP server.',
            inputSchema: new ObjectSchema(
                properties: [
                    'message' => new StringSchema(description: 'Optional message to echo back'),
                ]
            ),
            annotations: new Annotations(
                title: 'Ping',
                readOnlyHint: true,
                idempotentHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $message = $input['message'] ?? 'pong';
        return Content::text($message);
    }
}
