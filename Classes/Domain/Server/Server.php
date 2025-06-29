<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\ObjectManagement\ObjectManager;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\Client\Request\CompletionCompleteRequest;
use SJS\Neos\MCP\Domain\Client\Request\InitializeRequest;
use SJS\Neos\MCP\Domain\Client\Request\NotificationsCancelledRequest;
use SJS\Neos\MCP\Domain\Client\Request\ResourcesListRequest;
use SJS\Neos\MCP\Domain\Client\Request\ResourcesReadRequest;
use SJS\Neos\MCP\Domain\Client\Request\ResourcesTemplatesListRequest;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\Server\Method\CompletionCompleteMethod;
use SJS\Neos\MCP\Domain\Server\Method\InitializeMethod;
use SJS\Neos\MCP\Domain\Server\Method\ResourcesListMethod;
use SJS\Neos\MCP\Domain\Server\Method\ResourcesReadMethod;
use SJS\Neos\MCP\Domain\Server\Method\ResourcesTemplatesListMethod;
use SJS\Neos\MCP\FeatureSet\AbstractFeatureSet;
use SJS\Neos\MCP\Transport\JsonRPC;

#[Flow\Proxy(false)]
class Server
{
    /**
     * @var array<AbstractFeatureSet>
     */
    protected array $featureSets = [];

    public function __construct(
        public readonly string $name,
        public readonly array $configuration,
        public readonly ActionRequest $request,
        protected ObjectManager $objectManager,
        protected LoggerInterface $logger,
    ) {
        $featureSetsConfiguration = $configuration['featureSets'] ?? [];

        foreach ($featureSetsConfiguration as $featureSetName => $featureSetClass) {
            $featureSet = $this->objectManager->get($featureSetClass);

            if (!($featureSet instanceof AbstractFeatureSet)) {
                continue;
            }
            $featureSet->setActionRequest($request);
            $featureSet->initialize();
            $this->featureSets[$featureSetName] = $featureSet;
        }
    }

    public function handleRequest()
    {
        $rpcRequestData = $this->request->getArguments();

        $rpcRequestJson = json_encode($rpcRequestData, JSON_PRETTY_PRINT);
        $this->logger->debug("Request: {$rpcRequestJson}", LogEnvironment::fromMethodName(__METHOD__));

        $rpcRequest = JsonRPC\Request::fromArray($rpcRequestData);

        return match ($rpcRequest->method) {
            InitializeRequest::Method => $this->handleInitialize(InitializeRequest::fromJsonRPCRequest($rpcRequest)),
            ResourcesListRequest::Method => $this->handleResourcesList(ResourcesListRequest::fromJsonRPCRequest($rpcRequest)),
            ResourcesTemplatesListRequest::Method => $this->handleResourcesTemplatesList(ResourcesTemplatesListRequest::fromJsonRPCRequest($rpcRequest)),
            CompletionCompleteRequest::Method => $this->handleCompletionComplete(CompletionCompleteRequest::fromJsonRPCRequest($rpcRequest)),
            ResourcesReadRequest::Method => $this->handleResourcesRead(ResourcesReadRequest::fromJsonRPCRequest($rpcRequest)),
            NotificationsCancelledRequest::Method => "{}",
            default => throw new \Exception("Unknown request method: {$rpcRequest->method}")
        };
    }

    protected function handleInitialize(InitializeRequest $initializeRequest)
    {
        return InitializeMethod::handle($initializeRequest);
    }

    protected function handleResourcesList(ResourcesListRequest $resourcesListRequest)
    {
        $resources = [];

        foreach ($this->featureSets as $featureSet) {
            $resources = array_merge($resources, $featureSet->resourcesList($resourcesListRequest->cursor));
        }

        return ResourcesListMethod::handle($resourcesListRequest, $resources, null);
    }

    protected function handleResourcesTemplatesList(ResourcesTemplatesListRequest $resourcesTemplatesListRequest)
    {
        $templates = [];

        foreach ($this->featureSets as $featureSet) {
            $templates = array_merge($templates, $featureSet->resourcesTemplatesList());
        }

        return ResourcesTemplatesListMethod::handle($resourcesTemplatesListRequest, $templates);
    }

    protected function handleCompletionComplete(CompletionCompleteRequest $completionCompleteRequest)
    {
        $completion = new Completion(
            [],
            0,
            false
        );

        foreach ($this->featureSets as $featureSet) {
            $featureSetCompletion = $featureSet->completionComplete($completionCompleteRequest->argument, $completionCompleteRequest->ref);
            if ($featureSetCompletion) {
                $completion = $featureSetCompletion;
                break;
            }
        }

        return CompletionCompleteMethod::handle($completionCompleteRequest, $completion);
    }

    protected function handleResourcesRead(ResourcesReadRequest $resourcesReadRequest): string
    {
        $resources = [];
        foreach ($this->featureSets as $featureSet) {
            $resources = array_merge($resources, $featureSet->resourcesRead($resourcesReadRequest->uri));
        }

        return ResourcesReadMethod::handle($resourcesReadRequest, $resources);
    }
}
