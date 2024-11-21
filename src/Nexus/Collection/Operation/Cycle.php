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
interface Cycle
{
    /**
     * Cycle indefinitely over a collection of items.
     *
     * **NOTE:** Be careful when using `all()` after calling `cycle()` as this
     * will exhaust all available memory trying to convert an infinite
     * collection. Make sure to call `limit()` after `cycle()` before
     * outputting the items.
     *
     * @return CollectionInterface<TKey, T>
     */
    public function cycle(): CollectionInterface;
}
