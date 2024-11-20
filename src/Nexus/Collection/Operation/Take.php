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
interface Take
{
    /**
     * Takes the first items with `$length`
     * and returns them as a new collection.
     *
     * ```
     * Collection::wrap(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5])->take(3);
     * => Collection(['a' => 1, 'b' => 2, 'c' => 3])
     * ```
     *
     * @param int<0, max> $length Number of items to take from the start
     *
     * @return CollectionInterface<TKey, T>
     */
    public function take(int $length): CollectionInterface;
}
