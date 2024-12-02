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
interface Diff
{
    /**
     * Computes the difference of the collection against a series of other
     * iterables (i.e., collections, arrays, Traversable objects).
     *
     * @param iterable<mixed, T> ...$others
     *
     * @return CollectionInterface<TKey, T>
     */
    public function diff(iterable ...$others): CollectionInterface;
}
