<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;


use Neos\Flow\Annotations as Flow;


#[Flow\Proxy(false)]
class Resource implements \JsonSerializable
{
    public function __construct(
        public readonly string $uri,
        public readonly string $name,
        public readonly string $title,
        public readonly string $description,
        public readonly string $mimeType,
    ) {
    }

    public function jsonSerialize()
    {
        return [
            'uri' => $this->uri,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'mimeType' => $this->mimeType,
        ];
    }
}