<?php

declare(strict_types=1);

namespace App\Milestone\Application\DTO;

use Generator;
use InvalidArgumentException;

use function fclose;
use function feof;
use function fread;
use function is_resource;

class AttachmentReadStream
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct(
        $resource,
        public readonly ?string $mimeType = null,
    ) {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('$resource should be a resource');
        }

        $this->resource = $resource;
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    public function getIterator(int $chunkSize = 4096): Generator
    {
        while (!feof($this->resource)) {
            yield fread($this->resource, $chunkSize);
        }
    }
}
