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
interface Reject
{
    /**
     * Returns a new collection from items where `$predicate` returns false.
     *
     * @param null|(\Closure(T, TKey): bool) $predicate
     *
     * @return CollectionInterface<TKey, T>
     */
    public function reject(?\Closure $predicate = null): CollectionInterface;
}
