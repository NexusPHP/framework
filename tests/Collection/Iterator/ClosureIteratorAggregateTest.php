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

use Nexus\Collection\Iterator\ClosureIteratorAggregate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ClosureIteratorAggregate::class)]
#[Group('unit-test')]
final class ClosureIteratorAggregateTest extends TestCase
{
    public function testInitialisation(): void
    {
        $iterator = ClosureIteratorAggregate::from(
            static fn(string $item): iterable => yield $item,
            'a',
        );

        self::assertSame(['a'], iterator_to_array($iterator));
    }
}
