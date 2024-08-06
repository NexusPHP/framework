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

use Nexus\Clock\SystemClock;
use Nexus\PHPUnit\Tachycardia\Attribute\TimeLimit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SystemClock::class)]
#[Group('unit')]
final class SystemClockTest extends TestCase
{
    public function testTimezoneOfSystemClock(): void
    {
        $clock = new SystemClock('UTC');
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());

        $clock = new SystemClock(date_default_timezone_get());
        self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());

        $clock = new SystemClock(new \DateTimeZone('Asia/Manila'));
        self::assertSame('Asia/Manila', $clock->now()->getTimezone()->getName());
    }

    public function testNowMovesWithTime(): void
    {
        $clock = new SystemClock('UTC');
        $before = new \DateTimeImmutable();
        usleep(10);
        $now = $clock->now();
        usleep(10);
        $after = new \DateTimeImmutable();

        self::assertGreaterThan($before, $now);
        self::assertLessThan($after, $now);
    }

    #[TimeLimit(2.10)]
    public function testClockSleeps(): void
    {
        $clock = new SystemClock('UTC');
        $tz = $clock->now()->getTimezone()->getName();

        $before = (float) $clock->now()->format('U.u');
        $clock->sleep(2.0);
        $now = (float) $clock->now()->format('U.u');
        $clock->sleep(0.0001);
        $after = (float) $clock->now()->format('U.u');

        self::assertEqualsWithDelta(2.0, $now - $before, 0.05);
        self::assertLessThan($after, $now);
        self::assertSame($tz, $clock->now()->getTimezone()->getName());
    }

    public function testClockDoesNotSleepOnNegativeOrZeroSeconds(): void
    {
        $clock = new SystemClock('UTC');
        $tz = $clock->now()->getTimezone()->getName();

        $before = (float) $clock->now()->format('U.u');
        $clock->sleep(-2.0);
        $now = (float) $clock->now()->format('U.u');
        $clock->sleep(0.0);
        $after = (float) $clock->now()->format('U.u');

        // account for latency in execution time
        self::assertEqualsWithDelta(0.0, $now - $before, 0.01);
        self::assertEqualsWithDelta(0.0, $after - $now, 0.01);
        self::assertSame($tz, $clock->now()->getTimezone()->getName());
    }
}
