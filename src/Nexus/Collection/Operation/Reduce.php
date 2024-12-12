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

/**
 * @template TKey
 * @template T
 */
interface Reduce
{
    /**
     * Reduce a collection using a `$predicate`.
     *
     * The reduction function is passed an accumulator value and the current
     * iterator value and returns a new accumulator. The accumulator is
     * initialised to `$initial`.
     *
     * @template TAcc
     *
     * @param (\Closure(TAcc, T, TKey): TAcc) $predicate
     * @param TAcc                            $initial
     *
     * @return TAcc
     */
    public function reduce(\Closure $predicate, mixed $initial = null): mixed;
}
