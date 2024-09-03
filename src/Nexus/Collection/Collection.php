<?php

declare(strict_types=1);

/**
 * This file is part of the Nexus framework.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\Collection;

use Nexus\Collection\Iterator\ClosureIteratorAggregate;

/**
 * @template TKey
 * @template T
 *
 * @implements CollectionInterface<TKey, T>
 *
 * @immutable
 */
final class Collection implements CollectionInterface
{
    /**
     * @var ClosureIteratorAggregate<TKey, T>
     */
    private ClosureIteratorAggregate $innerIterator;

    /**
     * @param (\Closure(iterable<int, mixed>): iterable<TKey, T>) $callable
     * @param iterable<int, mixed>                                $parameter
     */
    public function __construct(\Closure $callable, iterable $parameter = [])
    {
        $this->innerIterator = ClosureIteratorAggregate::from($callable, $parameter);
    }

    public static function wrap(\Closure|iterable $items): CollectionInterface
    {
        if ($items instanceof \Closure) {
            return new self(static fn(): \Generator => yield from $items());
        }

        if ($items instanceof \Generator) {
            return new self(static fn(): \Generator => yield from new \NoRewindIterator($items));
        }

        if ($items instanceof \Traversable) {
            return new self(static fn(): \Generator => yield from $items);
        }

        return new self(static fn(): \Generator => yield from $items);
    }

    public function count(): int
    {
        return iterator_count($this);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->innerIterator->getIterator();
    }
}
