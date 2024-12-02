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
interface Map
{
    /**
     * Returns a new collection consisting of items transformed by applying
     * the mapping function `$predicate`.
     *
     * Only the values are passed to the closure. If you want to pass only
     * the keys, you need to use `CollectionInterface::mapKeys()`. If you
     * want both keys and values, you need `CollectionInterface::mapWithKey()`.
     *
     * @template U
     *
     * @param (\Closure(T): U) $predicate
     *
     * @return CollectionInterface<TKey, U>
     */
    public function map(\Closure $predicate): CollectionInterface;
}
