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

namespace Nexus\Collection\Iterator;

/**
 * @template TKey
 * @template T
 *
 * @implements \IteratorAggregate<TKey, T>
 *
 * @internal
 */
final class ClosureIteratorAggregate implements \IteratorAggregate
{
    /**
     * @template S
     *
     * @param (\Closure(S): iterable<TKey, T>) $callable
     * @param S                                $parameter
     */
    private function __construct(
        private \Closure $callable,
        private mixed $parameter,
    ) {}

    /**
     * @template VKey
     * @template V
     * @template U
     *
     * @param (\Closure(U): iterable<VKey, V>) $callable
     * @param U                                $parameter
     *
     * @return self<VKey, V>
     */
    public static function from(\Closure $callable, mixed $parameter): self
    {
        return new self($callable, $parameter);
    }

    public function getIterator(): \Traversable
    {
        yield from ($this->callable)($this->parameter);
    }
}
