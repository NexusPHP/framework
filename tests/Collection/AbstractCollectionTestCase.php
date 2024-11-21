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

namespace Nexus\Tests\Collection;

use Nexus\Collection\CollectionInterface;
use PHPUnit\Framework\TestCase;

/**
 * This test case tests the non-static methods of `CollectionInterface`.
 * Static methods should be tested individually by each concrete test case.
 *
 * @internal
 */
abstract class AbstractCollectionTestCase extends TestCase
{
    public function testAll(): void
    {
        $collection = $this->collection(static function (): iterable {
            yield 'a' => 1;

            yield 2 => 2;

            yield 3 => 3;

            yield null => 4;

            yield true => 5;

            yield false => 6;
        });

        self::assertSame([1, 2, 3, 4, 5, 6], $collection->all());
        self::assertSame(['a' => 1, 2 => 2, 3 => 3, '' => 4, 1 => 5, 0 => 6], $collection->all(true));

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Cannot access offset of type stdClass on array');
        $this->collection(static fn(): iterable => yield new \stdClass() => 5)->all(true);
    }

    public function testChunk(): void
    {
        $collection = $this->collection(['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame([['a' => 1, 'b' => 2, 'c' => 3]], $collection->chunk(3)->all(true));
        self::assertSame([['a' => 1, 'b' => 2], ['c' => 3]], $collection->chunk(2)->all(true));
        self::assertSame([['a' => 1], ['b' => 2], ['c' => 3]], $collection->chunk(1)->all(true));
    }

    public function testCount(): void
    {
        self::assertCount(5, $this->collection());
        self::assertCount(0, $this->collection([]));
        self::assertCount(2, $this->collection([1, 2]));
    }

    public function testCycle(): void
    {
        self::assertCount(8, $this->collection([1])->cycle()->limit(8));
    }

    public function testDrop(): void
    {
        $collection = $this->collection(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]);

        self::assertSame(['d' => 4, 'e' => 5], $collection->drop(3)->all(true));
        self::assertSame([], $collection->drop(5)->all(true));
    }

    public function testFilter(): void
    {
        $predicate = static fn(int $item): bool => $item > 2;
        $collection = $this->collection([0, 1, 2, 3, 4]);

        self::assertSame([3, 4], $collection->filter($predicate)->all());
        self::assertSame([3 => 3, 4 => 4], $collection->filter($predicate)->all(true));
        self::assertSame([1, 2, 3, 4], $collection->filter()->all());
        self::assertSame([], $collection->filter(static fn(int $item): bool => $item < 0)->all());
    }

    public function testFilterKeys(): void
    {
        $predicate = static fn(string $key): bool => \strlen($key) > 5;
        $collection = $this->collection(['apple' => 5, 'banana' => 6]);

        self::assertSame(['banana' => 6], $collection->filterKeys($predicate)->all(true));
        self::assertSame([2, 3, 4, 5], $this->collection()->filterKeys()->all());
    }

    public function testFilterWithKey(): void
    {
        $predicate = static fn(int $item, string $key): bool => str_starts_with($key, 'd') && $item > 2;
        $collection = $this->collection(['banana' => 3, 'apple' => 4, 'dates' => 5, 'dragon fruit' => 0]);

        self::assertSame(['dates' => 5], $collection->filterWithKey($predicate)->all(true));
        self::assertSame(['banana' => 3, 'apple' => 4, 'dates' => 5], $collection->filterWithKey()->all(true));
    }

    public function testKeys(): void
    {
        $collection = $this->collection(static function (): \Generator {
            yield 'bananas' => 5;

            yield 'apples' => 4;

            yield 'oranges' => 7;
        });

        self::assertSame(['bananas', 'apples', 'oranges'], $collection->keys()->all());
    }

    public function testLimit(): void
    {
        self::assertSame([1, 2, 3, 4, 5], $this->collection()->limit(5)->all());
        self::assertSame([1, 2, 3, 4], $this->collection()->limit(4)->all());
        self::assertSame([1, 2, 3], $this->collection()->limit(3)->all());
        self::assertSame([1, 2], $this->collection()->limit(2)->all());
        self::assertSame([1], $this->collection()->limit(1)->all());

        self::assertSame(
            [5, 5, 5, 5, 5, 5],
            $this->collection(static function (): iterable {
                // @phpstan-ignore while.alwaysTrue
                while (true) {
                    yield 5;
                }
            })->limit(6)->all(),
        );
    }

    public function testMap(): void
    {
        self::assertSame([1, 4, 9, 16, 25], $this->collection()->map(static fn(int $item): int => $item * $item)->all());
        self::assertSame(
            ['a' => '3', 'b' => '3', 'c' => '5', 'd' => '4', 'e' => '4'],
            $this->collection(['a' => 'one', 'b' => 'two', 'c' => 'three', 'd' => 'four', 'e' => 'five'])
                ->map(static fn(string $item): int => \strlen($item))
                ->map(static fn(int $length): string => (string) $length)
                ->all(true),
        );
    }

    public function testMapKeys(): void
    {
        self::assertSame(
            [7 => 5, 6 => 4, 5 => 6],
            $this->collection(['bananas' => 5, 'apples' => 4, 'limes' => 6])
                ->mapKeys(static fn(string $key): int => \strlen($key))
                ->all(true),
        );
    }

    public function testMapWithKey(): void
    {
        self::assertSame(
            ['a' => 1, 'aa' => 4, 'aaa' => 9],
            $this->collection(['a' => 1, 'aa' => 2, 'aaa' => 3])
                ->mapWithKey(static fn(int $item, string $key): int => $item * \strlen($key))
                ->all(true),
        );
    }

    public function testSlice(): void
    {
        $collection = $this->collection(['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['a' => 1, 'b' => 2, 'c' => 3], $collection->slice(1, 0)->all(true));
        self::assertSame(['b' => 2, 'c' => 3], $collection->slice(1)->all(true));
        self::assertSame(['b' => 2], $collection->slice(1, 1)->all(true));

        self::assertSame(
            [-3, -2],
            $this->collection([-5, -4, -3, -2, -1, 0])
                ->slice(2, 2)
                ->all(),
        );
    }

    public function testTake(): void
    {
        self::assertSame([5, 4, 3], $this->collection([5, 4, 3, 2, 1])->take(3)->all());
        self::assertSame(['a' => 1], $this->collection(['a' => 1, 'b' => 2])->take(1)->all(true));
    }

    public function testTap(): void
    {
        $stack = [];
        $this->collection(['a' => 'apple', 'b' => 'banana'])->tap(
            static function (string $item) use (&$stack): void {
                $stack[\strlen($item)] = [];
            },
            static function (string $item, string $key) use (&$stack): void {
                $stack[\strlen($item)][] = [$key, $item];
            },
        )->all();

        self::assertSame([
            5 => [['a', 'apple']],
            6 => [['b', 'banana']],
        ], $stack);
    }

    public function testValues(): void
    {
        $collection = $this->collection(static function (): \Generator {
            yield 'bananas' => 5;

            yield 'apples' => 4;

            yield 'oranges' => 7;
        });

        self::assertSame([5, 4, 7], $collection->values()->all(true));
    }

    public function testCollectionHasMethodsArrangedInParticularOrder(): void
    {
        $reflection = new \ReflectionClass($this->collection());
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $sortedPublicMethods = $publicMethods;
        usort(
            $sortedPublicMethods,
            static function (\ReflectionMethod $a, \ReflectionMethod $b): int {
                if ($a->isConstructor()) {
                    return -1;
                }

                if ($b->isConstructor()) {
                    return 1;
                }

                if ($a->isStatic() && ! $b->isStatic()) {
                    return -1;
                }

                if (! $a->isStatic() && $b->isStatic()) {
                    return 1;
                }

                return strcmp($a->getName(), $b->getName());
            },
        );
        $publicMethods = array_map(static fn(\ReflectionMethod $rm): string => $rm->getName(), $publicMethods);
        $sortedPublicMethods = array_map(static fn(\ReflectionMethod $rm): string => $rm->getName(), $sortedPublicMethods);

        self::assertSame($sortedPublicMethods, $publicMethods);
    }

    /**
     * @template TKey
     * @template T
     *
     * @param (\Closure(): iterable<TKey, T>)|iterable<TKey, T> $items
     *
     * @return CollectionInterface<TKey, T>
     */
    abstract protected function collection(\Closure|iterable $items = [1, 2, 3, 4, 5]): CollectionInterface;
}
