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
use Nexus\Collection\Iterator\ClosureIteratorAggregate;

use function PHPStan\Testing\assertType;

assertType(
    'Nexus\Collection\Iterator\ClosureIteratorAggregate<int, string>',
    ClosureIteratorAggregate::from(
        static fn(string $item): iterable => yield $item,
        'a',
    ),
);
assertType(
    // @todo Make PHPStan understand the key is 'int'
    'Nexus\Collection\Iterator\ClosureIteratorAggregate<mixed, string>',
    ClosureIteratorAggregate::from(
        static function (iterable $collection): iterable {
            foreach ($collection as $key => $item) {
                assertType('int', $key);

                yield $key => get_debug_type($item);
            }
        },
        Collection::wrap([1, new \stdClass()]),
    ),
);
