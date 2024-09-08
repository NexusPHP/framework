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
interface FilterKeys
{
    /**
     * Returns a new collection from keys where `$predicate` returns `true`.
     *
     * If `$predicate` is not provided, this will just check for non-falsey keys.
     *
     * ```
     * Collection::wrap([1, 2])->filterKeys()->all();
     * => [2]
     * ```
     *
     * @param null|(\Closure(TKey): bool) $predicate
     *
     * @return CollectionInterface<TKey, T>
     */
    public function filterKeys(?\Closure $predicate = null): CollectionInterface;
}
