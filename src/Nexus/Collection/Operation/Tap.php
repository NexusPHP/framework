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

namespace Nexus\Collection\Operation;

use Nexus\Collection\CollectionInterface;

/**
 * @template TKey
 * @template T
 */
interface Tap
{
    /**
     * Executes callbacks on each item of the collection.
     *
     * The passed callbacks are called with the value and key of
     * each item in the collection. The return value of the
     * callbacks are ignored.
     *
     * @param (\Closure(T, TKey): void) ...$callbacks
     *
     * @return CollectionInterface<TKey, T>
     */
    public function tap(\Closure ...$callbacks): CollectionInterface;
}
