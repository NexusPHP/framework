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
interface Keys
{
    /**
     * Returns a new collection from keys of the original collection as
     * the new values.
     *
     * @return CollectionInterface<int, TKey>
     */
    public function keys(): CollectionInterface;
}
