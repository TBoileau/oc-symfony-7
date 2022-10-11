<?php

declare(strict_types=1);

namespace App\Representation;

interface RepresentationFactoryInterface
{
    /**
     * @template T
     *
     * @param array<array-key, T>  $items
     * @param array<string, mixed> $parameters
     *
     * @return Representation<T>
     */
    public function create(
        string $name,
        array $items,
        int $page,
        int $limit,
        int $total,
        string $route,
        array $parameters = [],
    ): Representation;
}
