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

namespace Nexus\Tests\Option;

use Nexus\Option\None;
use Nexus\Option\NoneException;
use Nexus\Option\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(None::class)]
#[CoversClass(Some::class)]
#[Group('unit-test')]
final class OptionTest extends TestCase
{
    public function testOptionIsSome(): void
    {
        self::assertTrue((new Some(2))->isSome());
        self::assertFalse((new None())->isSome());
    }

    public function testOptionIsSomeAnd(): void
    {
        $predicate = static fn(int $x): bool => $x > 1;

        self::assertTrue((new Some(2))->isSomeAnd($predicate));
        self::assertFalse((new Some(0))->isSomeAnd($predicate));
        self::assertFalse((new None())->isSomeAnd($predicate));
    }

    public function testOptionIsNone(): void
    {
        self::assertFalse((new Some(2))->isNone());
        self::assertTrue((new None())->isNone());
    }

    public function testOptionUnwrap(): void
    {
        self::assertSame('air', (new Some('air'))->unwrap());

        $this->expectException(NoneException::class);
        $this->expectExceptionMessage('Attempting to unwrap a None option.');
        (new None())->unwrap();
    }

    public function testOptionUnwrapOr(): void
    {
        $value = 'car';
        $default = 'bike';

        self::assertSame($value, (new Some($value))->unwrapOr($default));
        self::assertSame($default, (new None())->unwrapOr($default));
    }

    public function testOptionUnwrapOrElse(): void
    {
        $value = 4;
        $default = static fn(): int => 20;

        self::assertSame($value, (new Some($value))->unwrapOrElse($default));
        self::assertSame($default(), (new None())->unwrapOrElse($default));
    }

    public function testOptionMap(): void
    {
        $predicate = strlen(...);

        $option = (new Some('Hello, World!'))->map($predicate);
        self::assertTrue($option->isSome());
        self::assertSame(13, $option->unwrap());

        $none = new None();
        $newNone = $none->map($predicate);
        self::assertTrue($newNone->isNone());
        self::assertNotSame($newNone, $none);
    }

    public function testOptionMapOr(): void
    {
        $predicate = strlen(...);

        self::assertSame(3, (new Some('foo'))->mapOr(42, $predicate));
        self::assertSame(42, (new None())->mapOr(42, $predicate));
    }

    public function testOptionMapOrElse(): void
    {
        $predicate = strlen(...);
        $default = static fn() => 42;

        self::assertSame(3, (new Some('foo'))->mapOrElse($default, $predicate));
        self::assertSame(42, (new None())->mapOrElse($default, $predicate));
    }

    public function testOptionAnd(): void
    {
        $none = new None();
        self::assertTrue((new Some(2))->and($none)->isNone());
        self::assertTrue($none->and(new Some('foo'))->isNone());
        self::assertTrue($none->and($none)->isNone());
        self::assertNotSame($none, $none->and($none));

        $option = (new Some(2))->and(new Some('foo'));
        self::assertInstanceOf(Some::class, $option);
        self::assertSame('foo', $option->unwrap());
    }

    public function testOptionAndThen(): void
    {
        $squareThenToString = static fn(int $number): Some => new Some((string) ($number ** 2));

        $option = (new Some(2))->andThen($squareThenToString);
        self::assertTrue($option->isSome());
        self::assertSame('4', $option->unwrap());

        $none = new None();
        self::assertTrue($none->andThen($squareThenToString)->isNone());
        self::assertNotSame($none, $none->andThen($squareThenToString));
    }

    public function testOptionFilter(): void
    {
        $isEven = static fn(int $n): bool => $n % 2 === 0;

        $none = new None();
        self::assertTrue($none->filter($isEven)->isNone());
        self::assertNotSame($none, $none->filter($isEven));

        self::assertFalse((new Some(3))->filter($isEven)->isSome());

        $some = new Some(4);
        self::assertTrue($some->filter($isEven)->isSome());
        self::assertNotSame($some, $some->filter($isEven));
    }

    public function testOptionOr(): void
    {
        $some02 = new Some(2);
        $some100 = new Some(100);
        $none = new None();

        self::assertTrue($some02->or($none)->isSome());
        self::assertSame(2, $some02->or($none)->unwrap());
        self::assertNotSame($some02, $some02->or($none));

        self::assertTrue($none->or($some100)->isSome());
        self::assertSame(100, $none->or($some100)->unwrap());

        self::assertTrue($some02->or($some100)->isSome());
        self::assertSame(2, $some02->or($some100)->unwrap());

        self::assertTrue($none->or($none)->isNone());
    }

    public function testOptionOrElse(): void
    {
        $nobody = static fn(): None => new None();
        $vikings = static fn(): Some => new Some('vikings');

        $some = new Some('barbarians');
        $option = $some->orElse($vikings);
        self::assertTrue($option->isSome());
        self::assertNotSame($some, $option);
        self::assertSame('barbarians', $option->unwrap());

        $option = (new None())->orElse($vikings);
        self::assertTrue($option->isSome());
        self::assertSame('vikings', $option->unwrap());

        self::assertTrue((new None())->orElse($nobody)->isNone());
    }

    public function testOptionXor(): void
    {
        $some = new Some(2);
        $none = new None();

        self::assertInstanceOf(Some::class, $some->xor($none));
        self::assertSame(2, $some->xor($none)->unwrap());
        self::assertNotSame($some, $some->xor($none));

        self::assertInstanceOf(Some::class, $none->xor($some));
        self::assertSame(2, $none->xor($some)->unwrap());

        self::assertInstanceOf(None::class, $some->xor($some));
        self::assertInstanceOf(None::class, $none->xor($none));
        self::assertNotSame($none, $none->xor($none));
    }

    public function testOptionIteration(): void
    {
        foreach (new Some(2) as $index => $value) {
            self::assertSame(0, $index);
            self::assertSame(2, $value);
        }

        self::assertTrue((new Some(2))->getIterator()->valid());
        self::assertFalse((new None())->getIterator()->valid());
    }
}
