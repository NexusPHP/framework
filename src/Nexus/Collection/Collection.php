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
use Nexus\Collection\Iterator\RewindableIterator;

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

    /**
     * @return self<int, non-empty-array<TKey, T>>
     */
    public function chunk(int $size): self
    {
        return new self(static function (iterable $collection) use ($size): iterable {
            $chunk = [];
            $count = 0;

            foreach ($collection as $key => $item) {
                $chunk[$key] = $item;
                ++$count;

                if ($count === $size) {
                    yield $chunk;

                    $chunk = [];
                    $count = 0;
                }
            }

            if ([] !== $chunk) {
                yield $chunk;
            }
        }, [$this]);
    }

    public function count(): int
    {
        return iterator_count($this);
    }

    /**
     * @return self<TKey, T>
     */
    public function cycle(): self
    {
        return new self(static function (iterable $collection): iterable {
            return new \InfiniteIterator(
                new RewindableIterator(
                    static function () use ($collection): \Generator {
                        yield from $collection;
                    },
                ),
            );
        }, [$this]);
    }

    /**
     * @return self<TKey, T>
     */
    public function diff(iterable ...$others): self
    {
        return new self(static function (iterable $collection) use ($others): iterable {
            $hashTable = [];
            $toArrayKey = static fn(mixed $input): string => \is_string($input) ? $input : (string) json_encode($input);

            foreach ($others as $other) {
                foreach ($other as $value) {
                    $hashTable[$toArrayKey($value)] = true;
                }
            }

            foreach ($collection as $key => $value) {
                if (! \array_key_exists($toArrayKey($value), $hashTable)) {
                    yield $key => $value;
                }
            }
        }, [$this]);
    }

    /**
     * @return self<TKey, T>
     */
    public function diffKey(iterable ...$others): self
    {
        return new self(static function (iterable $collection) use ($others): iterable {
            $hashTable = [];
            $toArrayKey = static fn(mixed $input): string => \is_string($input) ? $input : (string) json_encode($input);

            foreach ($others as $other) {
                foreach ($other as $key) {
                    $hashTable[$toArrayKey($key)] = true;
                }
            }

            foreach ($collection as $key => $value) {
                if (! \array_key_exists($toArrayKey($key), $hashTable)) {
                    yield $key => $value;
                }
            }
        }, [$this]);
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

    /**
     * @return self<T, TKey>
     */
    public function flip(): self
    {
        return new self(static function (iterable $collection): iterable {
            foreach ($collection as $key => $item) {
                yield $item => $key;
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
     * @return self<TKey, T>
     */
    public function limit(int $limit = -1, int $offset = 0): self
    {
        return new self(static function (iterable $collection) use ($limit, $offset): iterable {
            $iterator = static function () use ($collection): iterable {
                yield from $collection;
            };

            yield from new \LimitIterator($iterator(), $offset, $limit);
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
     * @return self<int, CollectionInterface<TKey, T>>
     */
    public function partition(\Closure $predicate): self
    {
        return new self(static function (iterable $collection) use ($predicate): iterable {
            yield $collection->filterWithKey($predicate);

            yield $collection->reject($predicate);
        }, [$this]);
    }

    /**
     * @return self<TKey, T>
     */
    public function reject(?\Closure $predicate = null): self
    {
        $predicate ??= static fn(mixed $item, mixed $key): bool => (bool) $item && (bool) $key;

        return new self(static function (iterable $collection) use ($predicate): iterable {
            foreach ($collection as $key => $item) {
                if (! $predicate($item, $key)) {
                    yield $key => $item;
                }
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
