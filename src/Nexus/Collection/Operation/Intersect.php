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
interface Intersect
{
    /**
     * Computes the intersection of the collection against other iterables.
     *
     * @param iterable<mixed, T> ...$others
     *
     * @return CollectionInterface<TKey, T>
     */
    public function intersect(iterable ...$others): CollectionInterface;
}
