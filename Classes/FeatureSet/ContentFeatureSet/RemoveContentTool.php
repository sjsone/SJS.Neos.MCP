<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\ContentFeatureSet;

use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;

class RemoveContentTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            name: 'remove_content',
            description: 'Removes a content node',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'Remove Content',
                destructiveHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        // TODO: implement
        return Content::text('Not yet implemented.');
    }
}
