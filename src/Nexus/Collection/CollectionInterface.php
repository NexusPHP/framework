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

namespace Nexus\Collection;

/**
 * @template TKey
 * @template T
 *
 * @extends \IteratorAggregate<TKey, T>
 * @extends Operation\All<TKey, T>
 * @extends Operation\Filter<TKey, T>
 * @extends Operation\FilterKeys<TKey, T>
 * @extends Operation\FilterWithKey<TKey, T>
 * @extends Operation\Keys<TKey, T>
 * @extends Operation\Map<TKey, T>
 * @extends Operation\Values<TKey, T>
 */
interface CollectionInterface extends
    \Countable,
    \IteratorAggregate,
    Operation\All,
    Operation\Filter,
    Operation\FilterKeys,
    Operation\FilterWithKey,
    Operation\Keys,
    Operation\Map,
    Operation\Values
{
    /**
     * @template WKey
     * @template W
     *
     * @param (\Closure(): iterable<WKey, W>)|iterable<WKey, W> $items
     *
     * @return self<WKey, W>
     */
    public static function wrap(\Closure|iterable $items): self;
}
