<?php

declare(strict_types=1);

namespace App\Hal;

interface CollectionFactoryInterface
{
    /**
     * @template T
     *
     * @param array<array-key, T>  $items
     * @param array<string, mixed> $parameters
     *
     * @return Collection<T>
     */
    public function create(
        string $name,
        array $items,
        int $page,
        int $limit,
        int $total,
        string $route,
        array $parameters = [],
    ): Collection;
}
