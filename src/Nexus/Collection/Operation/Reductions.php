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
interface Reductions
{
    /**
     * Returns a new collection consisting of the intermediate values
     * of reducing the collection using a `$predicate`.
     *
     * The reduction `$predicate` is passed an `$initial` accumulator
     * and the current collection value and returns a new accumulator.
     *
     * Reductions returns a list of every accumulator along the way.
     *
     * @template TAcc
     *
     * @param (\Closure(TAcc, T, TKey): TAcc) $predicate
     * @param TAcc                            $initial
     *
     * @return CollectionInterface<int, TAcc>
     */
    public function reductions(\Closure $predicate, mixed $initial = null): CollectionInterface;
}
