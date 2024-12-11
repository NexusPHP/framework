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
interface Associate
{
    /**
     * Combines the collection to another iterable.
     *
     * The values of the collection will be the keys of the new collection
     * while the values of the other will be the values.
     *
     * @template UKey
     * @template U
     *
     * @param iterable<UKey, U> $values
     *
     * @return CollectionInterface<T, U>
     *
     * @throws \InvalidArgumentException
     */
    public function associate(iterable $values): CollectionInterface;
}
