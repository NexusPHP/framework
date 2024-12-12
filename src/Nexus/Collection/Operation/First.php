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
interface First
{
    /**
     * Gets the first item in the collection that passes the given `$predicate`.
     *
     * If none was found, this returns the `$default` value.
     *
     * @template V
     *
     * @param (\Closure(T, TKey): bool) $predicate
     * @param V                         $default
     *
     * @return T|V
     */
    public function first(\Closure $predicate, mixed $default = null): mixed;
}
