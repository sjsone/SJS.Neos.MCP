<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller;

use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\Domain\Server\Server;
use SJS\Neos\MCP\Domain\Server\ServerFactory;
use SJS\Neos\MCP\Transport\JsonRPC\Response;

class MCPController extends ActionController
{
    #[Flow\Inject()]
    protected ServerFactory $serverFactory;

    protected $supportedMediaTypes = [
        'application/json',
        // 'text/event-stream',
    ];

    /**
     * @Flow\SkipCsrfProtection
     */
    {
        $this->response->setHttpHeader("Content-Type", "application/json");

        $server = $this->buildServerFromRequest();

        $response = $server->handleRequest();

        return $response;
    }

    protected function buildServerFromRequest(): ?Server
    {
        return $this->serverFactory->buildFromName(
            'mcp',
            $this->request
        );
    }
}
