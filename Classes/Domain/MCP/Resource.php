<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Resource implements \JsonSerializable
{
    // TODO: create ::forListing and ::forReadText and ::forReadBinary
    public function __construct(
        public readonly string $uri,
        public readonly string $name,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $mimeType = null,
        public readonly ?int $size = null,
        public readonly ?string $text = null,
        public readonly ?string $blob = null,
    ) {
    }

    public static function createBlobResource(
        string $uri,
        string $name,
        ?string $title = null,
        ?string $description = null,
        ?string $mimeType = null,
        ?int $size = null,
        mixed $data = null,
    ): self {
        return new self(
            $uri,
            $name,
            title: $title,
            description: $description,
            mimeType: $mimeType,
            size: $size,
            blob: base64_encode($data),
        );
    }

    public function jsonSerialize()
    {
        $data = [
            'uri' => $this->uri,
            'name' => $this->name,
        ];

        if ($this->title) {
            $data['title'] = $this->title;
        }

        if ($this->description) {
            $data['description'] = $this->description;
        }

        if ($this->mimeType) {
            $data['mimeType'] = $this->mimeType;
        }

        if ($this->size) {
            $data['size'] = $this->size;
        }

        if ($this->text) {
            $data['text'] = $this->text;
        }

        if ($this->blob) {
            $data['blob'] = $this->blob;
        }

        return $data;
    }
}
