<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures;

use Psr\Container\ContainerInterface;

use function array_key_exists;

/**
 * Minimal PSR-11 container backed by an array of services.
 */
final class Container implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(
        private readonly array $services = [],
    ) {
    }

    public function get(string $id): mixed
    {
        if (! $this->has($id)) {
            throw new ContainerNotFoundException($id);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
