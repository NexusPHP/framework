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
interface Forget
{
    /**
     * Removes items having the specified keys, then returns the remaining
     * items as a new collection.
     *
     * @param TKey ...$keys
     *
     * @return CollectionInterface<TKey, T>
     */
    public function forget(mixed ...$keys): CollectionInterface;
}
