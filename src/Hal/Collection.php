<?php

declare(strict_types=1);

namespace App\Hal;

use function count;

/**
 * @template T
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
final class Collection
{
    public int $count;

    /**
     * @var array<string, array{href: string}>
     */
    public array $_links = [];

    /**
     * @var array<string, array<array-key, T>>
     */
    public array $_embedded = [];

    public function __construct(
        public readonly int $page,
        public readonly int $pages,
        public readonly int $limit,
        public readonly int $total
    ) {
    }

    /**
     * @return $this<T>
     */
    public function addLinks(string $rel, string $href): self
    {
        $this->_links[$rel] = ['href' => $href];

        return $this;
    }

    /**
     * @param array<array-key, T> $items
     *
     * @return $this<T>
     */
    public function addEmbedded(string $rel, array $items): self
    {
        $this->_embedded[$rel] = $items;
        $this->count = count($items);

        return $this;
    }
}
