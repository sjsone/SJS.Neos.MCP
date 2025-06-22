<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;


use Neos\Flow\Annotations as Flow;


#[Flow\Proxy(false)]
class ResourceListing implements \JsonSerializable
{
    /**
     * @param array<Resource> $resources
     * @param null|string $nextCursor
     */
    public function __construct(
        public readonly array $resources,
        public readonly ?string $nextCursor = null,
    ) {
    }

    function jsonSerialize()
    {
        $data = [
            "resources" => $this->resources
        ];

        if ($this->nextCursor) {
            $data['nextCursor'] = $this->nextCursor;
        }

        return $data;
    }
}
