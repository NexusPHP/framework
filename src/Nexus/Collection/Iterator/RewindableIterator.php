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
 * @implements \Iterator<TKey, T>
 *
 * @see https://github.com/nikic/iter/blob/master/src/iter.rewindable.php
 *
 * @internal
 */
final class RewindableIterator implements \Iterator
{
    private \Iterator $iterator;

    /**
     * @param (\Closure(mixed...): \Iterator<TKey, T>) $callable
     * @param list<mixed>                              $args
     */
    public function __construct(
        private \Closure $callable,
        private array $args = [],
    ) {
        $this->rewind();
    }

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $callable = $this->callable;
        $this->iterator = ($callable)(...$this->args);
    }
}
