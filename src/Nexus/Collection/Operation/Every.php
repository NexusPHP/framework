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
interface Every
{
    /**
     * Returns `true` if all values in the collection satisfy the `$predicate`.
     *
     * This method is short-circuiting, i.e., if the predicate fails for the
     * first time, all remaining items will no longer be considered.
     *
     * @param (\Closure(T, TKey): bool) $predicate
     */
    public function every(\Closure $predicate): bool;
}
