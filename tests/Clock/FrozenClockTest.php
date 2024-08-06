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

namespace Nexus\Tests\Clock;

use Nexus\Clock\FrozenClock;
use Nexus\Clock\SystemClock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FrozenClock::class)]
#[Group('unit')]
final class FrozenClockTest extends TestCase
{
    public function testFrozenClockAlwaysReturnTheSameDate(): void
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $clock = new FrozenClock($now);

        self::assertSame($now, $clock->now());
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());

        (new SystemClock('UTC'))->sleep(0.50);
        self::assertSame($now, $clock->now());
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());
    }

    public function testFrozenClockSleepingJustAdvancesTheDate(): void
    {
        $timezone = new \DateTimeZone('Asia/Manila');
        $clock = new FrozenClock(new \DateTimeImmutable('2024-08-05 17:59:59.999', $timezone));
        self::assertSame('2024-08-05 17:59:59.999000', $clock->now()->format('Y-m-d H:i:s.u'));
        self::assertSame($timezone->getName(), $clock->now()->getTimezone()->getName());

        $clock->sleep(3.141592); // pi seconds
        self::assertSame('2024-08-05 18:00:03.140592', $clock->now()->format('Y-m-d H:i:s.u'));
        self::assertSame($timezone->getName(), $clock->now()->getTimezone()->getName());

        $clock->sleep(60 * 60 * 4); // 4 hours
        self::assertSame('2024-08-05 22:00:03.140592', $clock->now()->format('Y-m-d H:i:s.u'));
        self::assertSame($timezone->getName(), $clock->now()->getTimezone()->getName());
    }
}
