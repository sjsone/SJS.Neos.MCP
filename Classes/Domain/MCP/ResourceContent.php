<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;


use Neos\Flow\Annotations as Flow;


#[Flow\Proxy(false)]
class ResourceContent
{
    public function __construct(
        readonly string $uri,
        readonly string $name,
        readonly string $title,
        // readonly string $description,
        readonly string $mimeType,
        readonly string $text,
    ) {
    }

    public function jsonSerialize()
    {
        return [
            'uri' => $this->uri,
            'name' => $this->name,
            'title' => $this->title,
            // 'description' => $this->description,
            'mimeType' => $this->mimeType,
            'text' => $this->text,
        ];
    }
}