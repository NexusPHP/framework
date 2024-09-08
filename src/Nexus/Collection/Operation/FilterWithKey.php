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
interface FilterWithKey
{
    /**
     * Returns a new collection from keys and values where `$predicate` returns `true`.
     *
     * If `$predicate` is not provided, this just checks for non-falsey keys and values.
     *
     * ```
     * Collection::wrap([1, 0, 2, 5])->filterWithKey()->all();
     * => [2, 5]
     * ```
     *
     * @param null|(\Closure(T, TKey): bool) $predicate
     *
     * @return CollectionInterface<TKey, T>
     */
    public function filterWithKey(?\Closure $predicate = null): CollectionInterface;
}
