<?php

declare(strict_types=1);

namespace App\Hal;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CollectionFactory implements CollectionFactoryInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function create(
        string $name,
        array $items,
        int $page,
        int $limit,
        int $total,
        string $route,
        array $parameters = []
    ): Collection {
        $pages = (int) ceil($total / $limit);

        $representation = (new Collection($page, $pages, $limit, $total))
            ->addLinks(
                'self',
                $this->urlGenerator->generate(
                    $route,
                    ['page' => $page, 'limit' => $limit] + $parameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            )
            ->addEmbedded($name, $items)
        ;

        if ($page > 1) {
            $representation->addLinks(
                'first',
                $this->urlGenerator->generate(
                    $route,
                    ['page' => 1, 'limit' => $limit] + $parameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
            $representation->addLinks(
                'previous',
                $this->urlGenerator->generate(
                    $route,
                    ['page' => $page - 1, 'limit' => $limit] + $parameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
        }

        if ($page < $pages) {
            $representation->addLinks(
                'last',
                $this->urlGenerator->generate(
                    $route,
                    ['page' => $pages, 'limit' => $limit] + $parameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
            $representation->addLinks(
                'next',
                $this->urlGenerator->generate(
                    $route,
                    ['page' => $page + 1, 'limit' => $limit] + $parameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
        }

        return $representation;
    }
}
