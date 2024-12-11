<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\PhpDoc;

/**
 * @phpstan-template T
 */
class Baz {}

/**
 * @template T
 * @phpstan-extends Baz<T>
 * @phpstan-implements \IteratorAggregate<int, T>
 *
 * @phpstan-readonly-allow-private-mutation
 */
class Foo extends Baz implements \IteratorAggregate
{
    /**
     * @phpstan-var T
     */
    public readonly int $base;

    /**
     * @phpstan-param int<0, max> $baz
     * @param int<1, 3> $qux
     *
     * @phpstan-return T
     */
    public function bar(int $baz, int $qux): int
    {
        /** @var int<1, max> $baz */
        $baz += $qux;

        return $baz * $qux;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([1]);
    }
}
