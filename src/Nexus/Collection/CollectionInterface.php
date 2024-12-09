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
 * @extends Operation\Chunk<TKey, T>
 * @extends Operation\Cycle<TKey, T>
 * @extends Operation\Diff<TKey, T>
 * @extends Operation\DiffKey<TKey, T>
 * @extends Operation\Drop<TKey, T>
 * @extends Operation\Filter<TKey, T>
 * @extends Operation\FilterKeys<TKey, T>
 * @extends Operation\FilterWithKey<TKey, T>
 * @extends Operation\Flip<TKey, T>
 * @extends Operation\Intersect<TKey, T>
 * @extends Operation\Keys<TKey, T>
 * @extends Operation\Limit<TKey, T>
 * @extends Operation\Map<TKey, T>
 * @extends Operation\MapKeys<TKey, T>
 * @extends Operation\MapWithKey<TKey, T>
 * @extends Operation\Partition<TKey, T>
 * @extends Operation\Reject<TKey, T>
 * @extends Operation\Slice<TKey, T>
 * @extends Operation\Take<TKey, T>
 * @extends Operation\Tap<TKey, T>
 * @extends Operation\Values<TKey, T>
 */
interface CollectionInterface extends
    \Countable,
    \IteratorAggregate,
    Operation\All,
    Operation\Chunk,
    Operation\Cycle,
    Operation\Diff,
    Operation\DiffKey,
    Operation\Drop,
    Operation\Filter,
    Operation\FilterKeys,
    Operation\FilterWithKey,
    Operation\Flip,
    Operation\Intersect,
    Operation\Keys,
    Operation\Limit,
    Operation\Map,
    Operation\MapKeys,
    Operation\MapWithKey,
    Operation\Partition,
    Operation\Reject,
    Operation\Slice,
    Operation\Take,
    Operation\Tap,
    Operation\Values
{
    /**
     * @template WrapKey
     * @template Wrap
     *
     * @param (\Closure(): iterable<WrapKey, Wrap>)|iterable<WrapKey, Wrap> $items
     *
     * @return self<WrapKey, Wrap>
     */
    public static function wrap(\Closure|iterable $items): self;
}
