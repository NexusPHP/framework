<?php

declare(strict_types=1);

namespace Nexus\Tests\Result;

use Nexus\Result\Err;
use Nexus\Result\Ok;
use Nexus\Result\Result;

use function PHPStan\Testing\assertType;

$ok = new Ok(2);
$err = new Err('hey');
$predicate = static fn(string $v): int => (int) $v * 2;

assertType('Nexus\Result\Ok<int>', $ok->map($predicate));
assertType('Nexus\Result\Err<string>', $err->map($predicate));

function bar(Result $result): void
{
    if ($result->isOk()) {
        assertType('Nexus\Result\Ok<mixed>', $result);
        assertType('mixed', $result->unwrap());
        assertType('never', $result->unwrapErr());
    } else {
        assertType('Nexus\Result\Err<mixed>', $result);
        assertType('never', $result->unwrap());
        assertType('mixed', $result->unwrapErr());
    }
}

assertType('int', $ok->mapOr(42, $predicate));
assertType('int', $err->mapOr(42, $predicate));
assertType('int', $ok->mapOrElse(static fn(string $x): int => \strlen($x), $predicate));
assertType('int', $err->mapOrElse(static fn(string $x): int => \strlen($x), $predicate));
assertType('Nexus\Result\Ok<int>', $ok->mapErr(static fn(int $x): string => (string) $x));
assertType('Nexus\Result\Err<lowercase-string&non-falsy-string>', $err->mapErr(static fn(int $x): string => \sprintf('err: %d', $x)));

assertType('int', $ok->unwrap());
assertType('never', $err->unwrap());
assertType('never', $ok->unwrapErr());
assertType('string', $err->unwrapErr());

$x = new Ok(2);
$y = new Err('late error');
assertType('Nexus\Result\Err<string>', $x->and($y));
assertType('Nexus\Result\Ok<int>', $x->or($y));

$x = new Err('early error');
$y = new Ok('foo');
assertType('Nexus\Result\Err<string>', $x->and($y));
assertType('Nexus\Result\Ok<string>', $x->or($y));

$x = new Err('not a 2');
$y = new Err('late error');
assertType('Nexus\Result\Err<string>', $x->and($y));
assertType('Nexus\Result\Err<string>', $x->or($y));

$x = new Ok(2);
$y = new Ok('different result type');
assertType('Nexus\Result\Ok<string>', $x->and($y));
assertType('Nexus\Result\Ok<int>', $x->or($y));

$predicate = static fn(int $x): Ok => new Ok((float) $x);
assertType('Nexus\Result\Ok<float>', (new Ok(2))->andThen($predicate));
assertType('Nexus\Result\Ok<int>', (new Ok(2))->orElse($predicate));
assertType('Nexus\Result\Err<int>', (new Err(500))->andThen($predicate));
assertType('Nexus\Result\Ok<float>', (new Err(500))->orElse($predicate));
