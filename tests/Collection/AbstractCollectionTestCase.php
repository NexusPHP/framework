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
    public function testCount(): void
    {
        self::assertCount(5, $this->collection());
        self::assertCount(0, $this->collection([]));
        self::assertCount(2, $this->collection([1, 2]));
    }

    /**
     * @template TKey of array-key
     * @template T
     *
     * @param array<TKey, T> $items
     *
     * @return CollectionInterface<TKey, T>
     */
    abstract protected function collection(array $items = [1, 2, 3, 4, 5]): CollectionInterface;
}
