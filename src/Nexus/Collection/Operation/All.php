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
interface All
{
    /**
     * Turns a collection into its array representation.
     *
     * Care should be taken when using this method when the collection has potential
     * duplicate keys. In case keys are preserved, the last of the duplicate keys
     * will be retained and the previous keys will be lost.
     *
     * @param bool $preserveKeys Whether keys will be preserved during conversion.
     *                           Set this to `false` to prevent data loss when dealing
     *                           with duplicate keys.
     *
     * @return ($preserveKeys is false ? list<T> : array<TKey, T>)
     */
    public function all(bool $preserveKeys = false): array;
}
