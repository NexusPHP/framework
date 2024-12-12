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

/**
 * @template TKey
 * @template T
 */
interface Any
{
    /**
     * Returns `true` if any of the values in the collection
     * satisfies the `$predicate`.
     *
     * This method is short-circuiting, i.e., once the predicate
     * matches an item the remaining items will not be considered
     * anymore.
     *
     * @param (\Closure(T, TKey): bool) $predicate
     */
    public function any(\Closure $predicate): bool;
}
