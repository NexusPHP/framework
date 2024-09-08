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

namespace Nexus\Tests\Collection;

use Nexus\Collection\Collection;

use function PHPStan\Testing\assertType;

assertType(
    'Nexus\Collection\Collection<string, int>',
    new Collection(static function (): iterable {
        yield 'apple' => 1;
    }),
);
assertType('Nexus\Collection\Collection<int, string>', Collection::wrap(['a', 'b', 'c']));
assertType(
    'Nexus\Collection\Collection<string, bool>',
    Collection::wrap(static function (): iterable {
        yield 'a' => true;

        yield 'b' => true;

        yield 'c' => false;
    }),
);
assertType('Nexus\Collection\Collection<int, int>', Collection::wrap(new \ArrayIterator([1, 2])));

assertType('array<int>', Collection::wrap(['a' => 1, 'b' => 2])->all(true));
assertType('array<int, int>', Collection::wrap(['a' => 1, 'b' => 2])->all());

$collection = Collection::wrap(['apples' => 10, 'bananas' => 20]);
assertType('Nexus\Collection\Collection<int, string>', $collection->keys());
assertType('Nexus\Collection\Collection<int, int>', $collection->values());