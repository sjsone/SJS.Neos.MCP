<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP\Tool;

class Content implements \JsonSerializable
{
    protected array $content = [];
    protected ?array $structuredContent = null;

    public static function text(string $text): self
    {
        $content = new self();
        $content->addText($text);
        return $content;
    }

    public static function structured(array $structuredContent): self
    {
        $content = new self();
        $content->setStructuredContent($structuredContent);
        return $content;
    }

    public function __construct()
    {
    }

    public function addText(string $text)
    {
        $this->content[] = [
            "type" => "text",
            "text" => $text
        ];

        return $this;
    }

    public function setStructuredContent(array $data)
    {
        $this->structuredContent = $data;
    }

    public function jsonSerialize(): mixed
    {
        $data = [];

        if (!empty($this->content)) {
            $data["content"] = $this->content;
        }

        if ($this->structuredContent !== null) {
            $data["structuredContent"] = $this->structuredContent;
        }

        return $data;
    }
}
