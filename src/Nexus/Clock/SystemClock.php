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

namespace Nexus\Clock;

/**
 * A clock that relies on the system time.
 *
 * @immutable
 */
final readonly class SystemClock implements Clock
{
    private \DateTimeZone $timezone;

    public function __construct(\DateTimeZone|string $timezone)
    {
        $this->timezone = \is_string($timezone) ? new \DateTimeZone($timezone) : $timezone;
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', $this->timezone);
    }

    public function sleep(float|int $seconds): void
    {
        if ($seconds <= 0) {
            return;
        }

        $microseconds = (int) ($seconds * 1_000_000);
        $seconds = (int) floor($microseconds / 1_000_000);
        $microseconds %= 1_000_000;

        sleep($seconds);
        usleep($microseconds);
    }
}
