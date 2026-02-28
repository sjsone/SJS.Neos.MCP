<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\FlowFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Package\PackageManager;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class ListPackagesTool extends Tool
{
    #[Flow\Inject]
    protected PackageManager $packageManager;

    public function __construct()
    {
        parent::__construct(
            name: 'list_packages',
            description: 'Lists all available Flow/Neos packages with their composer name, version, and type',
            inputSchema: new ObjectSchema(
                properties: [
                    'type' => new StringSchema(
                        description: 'Filter by composer package type, e.g. "neos-package", "flow-package", "neos-site"'
                    ),
                    'filter' => new StringSchema(
                        description: 'Filter package keys by substring match'
                    ),
                ]
            ),
            annotations: new Annotations(
                title: 'List Packages',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $typeFilter = $input['type'] ?? null;
        $keyFilter = $input['filter'] ?? null;

        $packages = $this->packageManager->getAvailablePackages();

        $result = [];
        foreach ($packages as $packageKey => $package) {
            if ($keyFilter !== null && stripos($packageKey, $keyFilter) === false) {
                continue;
            }

            $manifest = $package->getComposerManifest();
            $packageType = $manifest['type'] ?? 'library';

            if ($typeFilter !== null && $packageType !== $typeFilter) {
                continue;
            }

            $result[$packageKey] = [
                'packageKey' => $packageKey,
                'composerName' => $manifest['name'] ?? $packageKey,
                'version' => $manifest['version'] ?? ($manifest['extra']['neos']['version'] ?? 'unknown'),
                'type' => $packageType,
                'description' => $manifest['description'] ?? '',
            ];
        }

        ksort($result);

        return Content::structured($result)
            ->addText(json_encode($result));
    }
}
