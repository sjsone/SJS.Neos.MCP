<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\CompletionCompleteMethod;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Completion;

#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    public function __construct(
        public readonly Completion $completion,
    ) {
    }

    public function jsonSerialize()
    {
        return [
            "completion" => $this->completion
        ];
    }
}
