<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\Server\Method\InitializeMethod;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class Result implements \JsonSerializable
{
    public function jsonSerialize()
    {

        return [
            "protocolVersion" => "2025-03-26",
            "capabilities" => [
                "resources" => [
                    "listChanged" => false,
                    "subscribe" => false,
                ],
                "completions" => (object) [],
                "tools" => (object) [],
            ],
            "instructions" => "do stuff",
            "serverInfo" => [
                "name" => "Neos MCP",
                "version" => "0.0.1",
            ]
        ];
    }
}
