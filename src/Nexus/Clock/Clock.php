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

use Psr\Clock\ClockInterface;

interface Clock extends ClockInterface
{
    /**
     * Lets the clock sleep for an amount of seconds.
     */
    public function sleep(float|int $seconds): void;
}
