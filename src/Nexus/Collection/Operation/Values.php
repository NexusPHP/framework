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
interface Values
{
    /**
     * Returns a new collection of values, ignoring the original keys.
     *
     * ```
     * Collection::wrap(['a' => 2, 'b' => 4])->values();
     * => Collection([2, 4])
     * ```
     *
     * @return CollectionInterface<int, T>
     */
    public function values(): CollectionInterface;
}
