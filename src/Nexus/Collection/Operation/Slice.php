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
interface Slice
{
    /**
     * Takes a slice from a collection and returns it
     * as a new collection.
     *
     * ```
     * Collection::wrap([-5, -4, -3, -2, -1, 0])->slice(2, 2);
     * => Collection([-3, -2])
     * ```
     *
     * @param int<0, max>      $start  Start offset
     * @param null|int<0, max> $length Length of collection (if not specified,
     *                                 all remaining values from the collection
     *                                 are used)
     *
     * @return CollectionInterface<TKey, T>
     */
    public function slice(int $start, ?int $length = null): CollectionInterface;
}
