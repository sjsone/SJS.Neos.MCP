<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Transport\JsonRPC;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Request
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $method,
        public readonly mixed $params = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        self::assertRequestData($data);

        return new self(
            $data['id'] ?? null,
            $data['method'],
            $data['params'] ?? null,
        );
    }

    protected static function assertRequestData(array $data, bool $withId = false)
    {
        $jsonRpc = $data['jsonrpc'] ?? null;
        if ($jsonRpc !== "2.0") {
            throw new \Exception("jsonrpc is not 2.0");
        }

        $method = $data['method'] ?? null;
        if ($method === null) {
            throw new \Exception("method required");
        }

        if ($withId) {
            $id = $data['id'] ?? null;
            if ($id === null) {
                throw new \Exception("id required");
            }
        }
    }

}
