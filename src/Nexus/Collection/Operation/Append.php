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
interface Append
{
    /**
     * Appends one or more `$items` into the collection and returns
     * a new collection.
     *
     * @template U
     *
     * @param U ...$items
     *
     * @return CollectionInterface<int|TKey, T|U>
     */
    public function append(mixed ...$items): CollectionInterface;
}
