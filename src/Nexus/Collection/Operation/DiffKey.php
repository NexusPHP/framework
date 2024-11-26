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
interface DiffKey
{
    /**
     * Computes the difference of the collection against other iterables
     * using the keys for comparison.
     *
     * ```
     * Collection::wrap(['a' => 1, 'b' => 2])->diff(['a'])->all(true);
     * => ['b' => 2]
     * ```
     *
     * @param iterable<mixed, TKey> ...$others
     *
     * @return CollectionInterface<TKey, T>
     */
    public function diffKey(iterable ...$others): CollectionInterface;
}
