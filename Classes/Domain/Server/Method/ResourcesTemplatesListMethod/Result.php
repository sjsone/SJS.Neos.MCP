<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\ResourcesTemplatesListMethod;


use Neos\Flow\Annotations as Flow;


#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    public function __construct(
        public readonly array $resourceTemplates,

    ) {
    }

    public function jsonSerialize()
    {
        return [
            "resourceTemplates" => $this->resourceTemplates,
        ];
    }
}