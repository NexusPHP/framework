<?php

declare(strict_types=1);

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
assertType('list<int>', Collection::wrap(['a' => 1, 'b' => 2])->all());

$collection = Collection::wrap(['apples' => 10, 'bananas' => 20]);
assertType('Nexus\Collection\Collection<int, string>', $collection->keys());
assertType('Nexus\Collection\Collection<int, int>', $collection->values());

assertType('Nexus\Collection\Collection<int, int>', Collection::wrap([10, 11])->map(static fn(int $item): int => $item ** 2));
assertType('Nexus\Collection\Collection<int, string>', Collection::wrap([1])->map(static fn(int $item): string => $item > 1 ? 'Yes' : 'No'));
assertType('Nexus\Collection\Collection<int<0, max>, int>', Collection::wrap(['apples' => 2])->mapKeys(static fn(string $key): int => \strlen($key)));
assertType('Nexus\Collection\Collection<int, int>', Collection::wrap([10])->mapWithKey(static fn(int $v, int $k): int => $v ** $k));

assertType('list<non-empty-array<int, int>>', Collection::wrap([5, 4, 3, 2, 1])->chunk(3)->all());
assertType('Nexus\Collection\Collection<int, non-empty-array<int, int>>', Collection::wrap([5, 4, 3, 2, 1])->chunk(4));

assertType('Nexus\Collection\Collection<float, string>', Collection::wrap(['a' => 1.5, 'b' => 2.5])->flip());

$collection = Collection::wrap([1, 2, 3, 4])->partition(static fn(int $v): bool => $v > 2);
assertType('Nexus\Collection\Collection<int, Nexus\Collection\CollectionInterface<int, int>>', $collection);

$collection = Collection::wrap([1, 2, 3]);
assertType('Nexus\Collection\Collection<int, int>', $collection);
assertType('Nexus\Collection\Collection<int, float>', $collection->associate([1.0, 2.0, 3.0]));
