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
interface Get
{
    /**
     * Gets the specific item from the collection having the specified key.
     * If none exists, returns the default value.
     *
     * @template V
     *
     * @param TKey $key
     * @param V    $default
     *
     * @return T|V
     */
    public function get(mixed $key, mixed $default = null): mixed;
}
