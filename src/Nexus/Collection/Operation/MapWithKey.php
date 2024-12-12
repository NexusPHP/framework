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
interface MapWithKey
{
    /**
     * Returns a new collection consisting of items transformed by the
     * mapping function `$predicate` that accepts the keys and values
     * as inputs. The keys are left unchanged.
     *
     * @template U
     *
     * @param (\Closure(T, TKey): U) $predicate
     *
     * @return CollectionInterface<TKey, U>
     */
    public function mapWithKey(\Closure $predicate): CollectionInterface;
}
