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

namespace Nexus\Tests\Collection\Iterator;

use Nexus\Collection\Iterator\RewindableIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RewindableIterator::class)]
#[Group('unit-test')]
final class RewindableIteratorTest extends TestCase
{
    public function testRewindableIteratorWorksAsIntended(): void
    {
        $iterator = new RewindableIterator(static function (): iterable {
            yield 0;

            yield 1;

            yield 2;
        });

        self::assertTrue($iterator->valid());
        self::assertSame([0, 0], [$iterator->key(), $iterator->current()]);
        $iterator->next();
        self::assertSame([1, 1], [$iterator->key(), $iterator->current()]);
        $iterator->next();
        self::assertSame([2, 2], [$iterator->key(), $iterator->current()]);
        $iterator->next();
        self::assertFalse($iterator->valid());

        $iterator->rewind();
        self::assertTrue($iterator->valid());
        self::assertSame([0, 0], [$iterator->key(), $iterator->current()]);
    }
}
