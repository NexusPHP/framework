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
interface Limit
{
    /**
     * Limit the number of values in the collection.
     *
     * @param int<-1, max> $limit
     * @param int<0, max>  $offset
     *
     * @return CollectionInterface<TKey, T>
     */
    public function limit(int $limit = -1, int $offset = 0): CollectionInterface;
}
