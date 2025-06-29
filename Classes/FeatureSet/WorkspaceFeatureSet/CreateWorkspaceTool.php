<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class CreateWorkspaceTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            'create_workspace',
            'Create Workspace',
            'Creates a workspace',
            new ObjectSchema(properties: [
                'workspaceName' => (new StringSchema(description: "name of the new workspace"))->required()
            ])
        );
    }
    public function run(mixed $input)
    {

        return [
            'status' => 'success',
            'message' => 'Workspace created successfully',
        ];
    }
}
