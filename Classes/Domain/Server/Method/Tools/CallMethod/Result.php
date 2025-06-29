<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\Tools\CallMethod;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    public function __construct(
        public readonly mixed $content,
    ) {
    }

    public function jsonSerialize()
    {
        return [
            'content' => $this->content
        ];
    }
}
