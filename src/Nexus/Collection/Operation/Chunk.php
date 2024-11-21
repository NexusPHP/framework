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
interface Chunk
{
    /**
     * Returns a new collection containing the original items in the
     * collection split into chunks of given `$size`.
     *
     * This chunking operation preserves the keys. If the original
     * collection does not split evenly, the final chunk will be
     * smaller.
     *
     * ```
     * Collection::wrap([5, 4, 3, 2, 1])->chunk(3);
     * => Collection([[5, 4, 3], [2, 1]])
     * ```
     *
     * @param int<1, max> $size
     *
     * @return CollectionInterface<int, non-empty-array<TKey, T>>
     */
    public function chunk(int $size): CollectionInterface;
}
