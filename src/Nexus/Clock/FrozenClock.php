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
 * A clock that always shows the same time.
 */
final class FrozenClock implements Clock
{
    public function __construct(
        private \DateTimeImmutable $now,
    ) {}

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }

    public function sleep(float|int $seconds): void
    {
        $now = $this->now->format('U.u') + $seconds;
        $now = \sprintf('@%0.6F', $now);
        $timezone = $this->now->getTimezone();

        $this->now = (new \DateTimeImmutable($now))->setTimezone($timezone);
    }
}
