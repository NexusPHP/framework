<?php

declare(strict_types=1);

namespace Nexus\Tests\Option;

use Nexus\Option\None;
use Nexus\Option\Option;
use Nexus\Option\Some;

use function Nexus\Option\option;
use function PHPStan\Testing\assertType;

assertType(None::class, option(null));
assertType(None::class, new None());
assertType('Nexus\Option\Some<null>', option(null, false));
assertType('Nexus\Option\Some<int>', new Some(2));

function testOption(Option $option): void
{
    if ($option->isSome()) {
        assertType('Nexus\Option\Some<mixed>', $option);
        assertType('false', $option->isNone());
    } else {
        assertType(None::class, $option);
        assertType('true', $option->isNone());
    }
}

assertType('string', (new Some('car'))->unwrap());
assertType('string', (new Some('car'))->unwrapOr(100));
assertType('string', (new Some('car'))->unwrapOrElse(static fn(): bool => true));
assertType('never', (new None())->unwrap());
assertType('100', (new None())->unwrapOr(100));
assertType('true', (new None())->unwrapOrElse(static fn(): bool => true));

$mapper = static fn(string $item): int => \strlen($item);
$default = static fn(): int => 12;
assertType('Nexus\Option\Some<int<0, max>>', (new Some('car'))->map($mapper));
assertType('int<0, max>', (new Some('car'))->mapOr($default(), $mapper));
assertType('int<0, max>', (new Some('car'))->mapOrElse($default, $mapper));
assertType(None::class, (new None())->map($mapper));
assertType('12', (new None())->mapOr($default(), $mapper));
assertType('12', (new None())->mapOrElse($default, $mapper));

assertType(None::class, (new Some('car'))->and(new None()));
assertType(None::class, (new None())->and(new Some('car')));
assertType('Nexus\Option\Some<bool>', (new Some('car'))->and(new Some(true)));

$option = (new Some(2))->andThen(static fn(int $number): Some => new Some((string) ($number ** 2)));
assertType('Nexus\Option\Some<numeric-string&uppercase-string>', $option);
assertType('numeric-string&uppercase-string', $option->unwrap());

assertType('Nexus\Option\Some<int>', (new Some(2))->or(new None()));
assertType('Nexus\Option\Some<int>', (new Some(2))->or(new Some('car')));
assertType('Nexus\Option\Some<bool>', (new None())->or(new Some(false)));
assertType(None::class, (new None())->or(new None()));

$nobody = static fn(): None => new None();
$vikings = static fn(): Some => new Some('vikings');
assertType('Nexus\Option\Some<string>', $vikings()->orElse($nobody));
assertType('Nexus\Option\Some<string>', $nobody()->orElse($vikings));

$some = new Some(2);
$none = new None();
assertType('Nexus\Option\Some<int>', $some->xor($none));
assertType('Nexus\Option\Some<int>', $none->xor($some));
assertType(None::class, $some->xor($some));
assertType(None::class, $none->xor($none));
