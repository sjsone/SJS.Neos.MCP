<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\ObjectManagement\ObjectManager;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\Client\Request;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\Server\Method;
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
            Request\InitializeRequest::Method => $this->handleInitialize(Request\InitializeRequest::fromJsonRPCRequest($rpcRequest)),
            Request\Resources\ListRequest::Method => $this->handleResourcesList(Request\Resources\ListRequest::fromJsonRPCRequest($rpcRequest)),
            Request\Resources\Templates\ListRequest::Method => $this->handleResourcesTemplatesList(Request\Resources\Templates\ListRequest::fromJsonRPCRequest($rpcRequest)),
            Request\Resources\ReadRequest::Method => $this->handleResourcesRead(Request\Resources\ReadRequest::fromJsonRPCRequest($rpcRequest)),
            Request\Tools\ListRequest::Method => $this->handleToolsList(Request\Tools\ListRequest::fromJsonRPCRequest($rpcRequest)),
            Request\Completion\CompleteRequest::Method => $this->handleCompletionComplete(Request\Completion\CompleteRequest::fromJsonRPCRequest($rpcRequest)),
            Request\Notifications\CancelledRequest::Method => "{}",
            default => throw new \Exception("Unknown request method: {$rpcRequest->method}")
        };
    }

    protected function handleInitialize(Request\InitializeRequest $initializeRequest)
    {
        return Method\InitializeMethod::handle($initializeRequest);
    }

    protected function handleResourcesList(Request\Resources\ListRequest $resourcesListRequest)
    {
        $resources = [];

        foreach ($this->featureSets as $featureSet) {
            $resources = array_merge($resources, $featureSet->resourcesList($resourcesListRequest->cursor));
        }

        return Method\Resources\ListMethod::handle($resourcesListRequest, $resources, null);
    }

    protected function handleResourcesTemplatesList(Request\Resources\Templates\ListRequest $resourcesTemplatesListRequest)
    {
        $templates = [];

        foreach ($this->featureSets as $featureSet) {
            $templates = array_merge($templates, $featureSet->resourcesTemplatesList());
        }

        return Method\Resources\Templates\ListMethod::handle($resourcesTemplatesListRequest, $templates);
    }

    protected function handleCompletionComplete(Request\Completion\CompleteRequest $completionCompleteRequest)
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

        return Method\Completion\CompleteMethod::handle($completionCompleteRequest, $completion);
    }

    protected function handleResourcesRead(Request\Resources\ReadRequest $resourcesReadRequest): string
    {
        $resources = [];
        foreach ($this->featureSets as $featureSet) {
            $resources = array_merge($resources, $featureSet->resourcesRead($resourcesReadRequest->uri));
        }

        return Method\Resources\ReadMethod::handle($resourcesReadRequest, $resources);
    }

    protected function handleToolsList(Request\Tools\ListRequest $toolsListRequest): string
    {
        $tools = [];
        foreach ($this->featureSets as $featureSet) {

        }

        return Method\Tools\ListMethod::handle($toolsListRequest, $tools, null);
    }
}
