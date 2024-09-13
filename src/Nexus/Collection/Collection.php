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
     * @template S
     *
     * @param (\Closure(S): iterable<TKey, T>) $callable
     * @param iterable<int, S>                 $parameter
     */
    public function __construct(\Closure $callable, iterable $parameter = [])
    {
        $this->innerIterator = ClosureIteratorAggregate::from($callable, ...$parameter);
    }

    /**
     * @template WKey
     * @template W
     *
     * @param (\Closure(): iterable<WKey, W>)|iterable<WKey, W> $items
     *
     * @return self<WKey, W>
     */
    public static function wrap(\Closure|iterable $items): self
    {
        if ($items instanceof \Closure) {
            return new self(static fn(): iterable => yield from $items());
        }

        return new self(static fn(): iterable => yield from $items);
    }

    /**
     * @return ($preserveKeys is false ? list<T> : array<array-key, T>)
     */
    public function all(bool $preserveKeys = false): array
    {
        return iterator_to_array($this, $preserveKeys);
    }

    public function count(): int
    {
        return iterator_count($this);
    }

    /**
     * @param null|(\Closure(T): bool) $predicate
     *
     * @return self<TKey, T>
     */
    public function filter(?\Closure $predicate = null): self
    {
        $predicate ??= static fn(mixed $item): bool => (bool) $item;

        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                if ($predicate($item)) {
                    yield $key => $item;
                }
            }
        }, [$this]);
    }

    /**
     * @param null|(\Closure(TKey): bool) $predicate
     *
     * @return self<TKey, T>
     */
    public function filterKeys(?\Closure $predicate = null): self
    {
        $predicate ??= static fn(mixed $key): bool => (bool) $key;

        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                if ($predicate($key)) {
                    yield $key => $item;
                }
            }
        }, [$this]);
    }

    /**
     * @param null|(\Closure(T, TKey): bool) $predicate
     *
     * @return self<TKey, T>
     */
    public function filterWithKey(?\Closure $predicate = null): self
    {
        $predicate ??= static fn(mixed $item, mixed $key): bool => (bool) $item && (bool) $key;

        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                if ($predicate($item, $key)) {
                    yield $key => $item;
                }
            }
        }, [$this]);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->innerIterator->getIterator();
    }

    /**
     * @return self<int, TKey>
     */
    public function keys(): self
    {
        return new self(static function (iterable $collection): iterable {
            foreach ($collection as $key => $_) {
                yield $key;
            }
        }, [$this]);
    }

    /**
     * @template U
     *
     * @param (\Closure(T): U) $predicate
     *
     * @return self<TKey, U>
     */
    public function map(\Closure $predicate): self
    {
        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                yield $key => $predicate($item);
            }
        }, [$this]);
    }

    /**
     * @template UKey
     *
     * @param (\Closure(TKey): UKey) $predicate
     *
     * @return self<UKey, T>
     */
    public function mapKeys(\Closure $predicate): self
    {
        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                yield $predicate($key) => $item;
            }
        }, [$this]);
    }

    /**
     * @return self<int, T>
     */
    public function values(): self
    {
        return new self(static function (iterable $collection): iterable {
            foreach ($collection as $item) {
                yield $item;
            }
        }, [$this]);
    }
}
