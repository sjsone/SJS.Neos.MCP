<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Client\Request\CompletionCompleteRequest;


use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Transport\JsonRPC\Request;


#[Flow\Proxy(false)]
class Argument
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['value'],
        );
    }
}