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
interface MapKeys
{
    /**
     * Returns a new collection consisting of items transformed by the
     * mapping function `$predicate` that accepts the keys as inputs.
     *
     * ```
     * Collection::wrap([1, 2, 3])->mapKeys(static fn(int $k): int => $k ** 2);
     * => Collection([0 => 1, 1 => 2, 4 => 3])
     * ```
     *
     * @template UKey
     *
     * @param (\Closure(TKey): UKey) $predicate
     *
     * @return CollectionInterface<UKey, T>
     */
    public function mapKeys(\Closure $predicate): CollectionInterface;
}
