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


class ListNodeTypesTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'list_node_types',
            description: 'Lists all available NodeTypes',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Node Types',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $httpRequest = $actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $nodeTypes = [];
        foreach ($contentRepository->getNodeTypeManager()->getNodeTypes(true) as $nodeType) {
            $nodeTypes[(string) $nodeType->name] = [
                'label' => $nodeType->getLabel(),
                'abstract' => $nodeType->isAbstract(),
                'final' => $nodeType->isFinal(),
                'description' => $nodeType->getConfiguration('description') ?? '',
            ];
        }

        return Content::structured($nodeTypes)->addText(json_encode($nodeTypes));
    }
}
