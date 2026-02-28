<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\NodeTypeFeatureSet;

use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;


class GetNodeTypeTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'get_node_type',
            description: 'Returns full configuration for a specific NodeType',
            inputSchema: new ObjectSchema(properties: [
                'name' => (new StringSchema(description: 'The NodeType name, e.g. Neos.Demo:Document'))->required(),
            ]),
            annotations: new Annotations(
                title: 'Get Node Type',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $httpRequest = $actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $nodeType = $contentRepository->getNodeTypeManager()->getNodeType($input['name']);

        $fullConfig = $nodeType->getFullConfiguration();

        return Content::structured($fullConfig)->addText(json_encode($fullConfig, JSON_UNESCAPED_SLASHES));
    }
}
