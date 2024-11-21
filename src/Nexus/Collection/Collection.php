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
     * @template WrapKey
     * @template Wrap
     *
     * @return self<WrapKey, Wrap>
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
     * @return self<TKey, T>
     */
    public function drop(int $length): self
    {
        return $this->slice($length);
    }

    /**
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
     * @template U
     *
     * @return self<TKey, U>
     */
    public function mapWithKey(\Closure $predicate): self
    {
        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                yield $key => $predicate($item, $key);
            }
        }, [$this]);
    }

    /**
     * @return self<TKey, T>
     */
    public function slice(int $start, ?int $length = null): self
    {
        return new self(static function (iterable $collection) use ($start, $length): iterable {
            if (0 === $length) {
                yield from $collection;

                return;
            }

            $i = 0;

            foreach ($collection as $key => $item) {
                if ($i++ < $start) {
                    continue;
                }

                yield $key => $item;

                if (null !== $length && $i >= $start + $length) {
                    break;
                }
            }
        }, [$this]);
    }

    /**
     * @return self<TKey, T>
     */
    public function take(int $length): self
    {
        return $this->slice(0, $length);
    }

    /**
     * @return self<TKey, T>
     */
    public function tap(\Closure ...$callbacks): self
    {
        return new self(static function (iterable $collection) use ($callbacks): iterable {
            foreach ($collection as $key => $item) {
                foreach ($callbacks as $callback) {
                    $callback($item, $key);
                }
            }

            yield from $collection;
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
