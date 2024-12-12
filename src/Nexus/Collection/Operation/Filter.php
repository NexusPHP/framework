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
interface Filter
{
    /**
     * Returns a new collection from items where `$predicate` returns `true`.
     *
     * If no `$predicate` is provided, this will just check for non-falsey values.
     *
     * @param null|(\Closure(T): bool) $predicate
     *
     * @return CollectionInterface<TKey, T>
     */
    public function filter(?\Closure $predicate = null): CollectionInterface;
}
