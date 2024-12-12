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
interface Has
{
    /**
     * Checks if the collection has an item with the specified key.
     *
     * @param TKey $key
     */
    public function has(mixed $key): bool;
}
