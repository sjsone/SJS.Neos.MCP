<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\FlowFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class ListConfigurationTreeTool extends Tool
{
    #[Flow\Inject]
    protected ConfigurationManager $configurationManager;

    public function __construct()
    {
        parent::__construct(
            name: 'list_configuration_tree',
            description: 'Returns the merged Flow configuration tree for a given type and optional path. Types: Settings, Routes, Policy, Caches, Objects.',
            inputSchema: new ObjectSchema(
                properties: [
                    'type' => new StringSchema(
                        description: 'Configuration type: Settings, Routes, Policy, Caches, Objects',
                        default: 'Settings'
                    ),
                    'path' => new StringSchema(
                        description: 'Dot-separated configuration path to narrow results, e.g. "Neos.Neos" or "SJS.Neos.MCP.server"'
                    ),
                ]
            ),
            annotations: new Annotations(
                title: 'List Configuration Tree',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $type = $input['type'] ?? ConfigurationManager::CONFIGURATION_TYPE_SETTINGS;
        $path = $input['path'] ?? null;

        $availableTypes = $this->configurationManager->getAvailableConfigurationTypes();

        if (!\in_array($type, $availableTypes, true)) {
            return Content::text(
                "Unknown configuration type '{$type}'. Available types: " . implode(', ', $availableTypes)
            );
        }

        $configuration = $this->configurationManager->getConfiguration($type, $path);

        if ($configuration === null) {
            return Content::text("No configuration found for type '{$type}'" . ($path ? " at path '{$path}'" : '') . '.');
        }

        $encoded = json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return Content::structured(\is_array($configuration) ? $configuration : ['_value' => $configuration])
            ->addText($encoded);
    }
}
