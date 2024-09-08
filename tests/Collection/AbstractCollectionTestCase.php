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

    public function testCount(): void
    {
        self::assertCount(5, $this->collection());
        self::assertCount(0, $this->collection([]));
        self::assertCount(2, $this->collection([1, 2]));
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

    public function testKeys(): void
    {
        $collection = $this->collection(static function (): \Generator {
            yield 'bananas' => 5;

            yield 'apples' => 4;

            yield 'oranges' => 7;
        });

        self::assertSame(['bananas', 'apples', 'oranges'], $collection->keys()->all());
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
