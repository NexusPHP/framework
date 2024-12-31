<?php

declare(strict_types=1);

namespace Nexus\Tests\Collection;

use Nexus\Collection\Collection;
use Nexus\Collection\Iterator\ClosureIteratorAggregate;
use Nexus\Collection\Iterator\RewindableIterator;

use function PHPStan\Testing\assertType;

assertType(
    'Nexus\Collection\Iterator\ClosureIteratorAggregate<int, \'a\'>',
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

$rewindableIterator = new RewindableIterator(
    static function (): iterable {
        yield 'a' => 1;

        yield 'b' => 2;
    },
);

assertType('Nexus\Collection\Iterator\RewindableIterator<string, int>', $rewindableIterator);
assertType('int', $rewindableIterator->current());
assertType('string', $rewindableIterator->key());
