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
interface Partition
{
    /**
     * Partition the collection into 2 tuples of collections.
     *
     * The first inner collection consists of items that have passed the `$predicate`.
     * The last inner collection consists of items that have failed the `$predicate`.
     *
     * @param (\Closure(T, TKey): bool) $predicate
     *
     * @return CollectionInterface<int, CollectionInterface<TKey, T>>
     */
    public function partition(\Closure $predicate): CollectionInterface;
}
