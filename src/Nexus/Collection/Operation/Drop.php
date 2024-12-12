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
interface Drop
{
    /**
     * Drops the first items with `$length` from the collection
     * and returns a new collection on the remaining items.
     *
     * @param int<0, max> $length Number of items to drop from the start
     *
     * @return CollectionInterface<TKey, T>
     */
    public function drop(int $length): CollectionInterface;
}
