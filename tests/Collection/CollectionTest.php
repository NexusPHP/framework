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

use Nexus\Collection\Collection;
use Nexus\Collection\CollectionInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 */
#[CoversClass(Collection::class)]
#[Group('unit-test')]
final class CollectionTest extends AbstractCollectionTestCase
{
    public function testWrap(): void
    {
        $expected = [1, 2, 3, 4, 5];

        self::assertSame($expected, iterator_to_array(Collection::wrap($expected)));
        self::assertSame($expected, iterator_to_array(Collection::wrap(new \ArrayIterator($expected))));
        self::assertSame($expected, iterator_to_array(Collection::wrap(static fn(): \Generator => yield from $expected)));
        self::assertSame($expected, iterator_to_array(Collection::wrap((static fn(): \Generator => yield from $expected)())));
    }

    protected function collection(\Closure|iterable $items = [1, 2, 3, 4, 5]): CollectionInterface
    {
        return Collection::wrap($items);
    }
}
