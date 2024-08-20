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
        self::assertInstanceOf(Some::class, $option);
        self::assertSame(13, $option->unwrap());

        self::assertInstanceOf(None::class, (new None())->map($predicate));
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
        self::assertInstanceOf(None::class, (new Some(2))->and(new None()));
        self::assertInstanceOf(None::class, (new None())->and(new Some('foo')));
        self::assertInstanceOf(None::class, (new None())->and(new None()));

        $option = (new Some(2))->and(new Some('foo'));
        self::assertInstanceOf(Some::class, $option);
        self::assertSame('foo', $option->unwrap());
    }

    public function testOptionAndThen(): void
    {
        $squareThenToString = static fn(int $number): Some => new Some((string) ($number ** 2));

        $option = (new Some(2))->andThen($squareThenToString);
        self::assertInstanceOf(Some::class, $option);
        self::assertSame('4', $option->unwrap());
        self::assertInstanceOf(None::class, (new None())->andThen($squareThenToString));
    }

    public function testOptionFilter(): void
    {
        $isEven = static fn(int $n): bool => $n % 2 === 0;

        self::assertInstanceOf(None::class, (new None())->filter($isEven));
        self::assertInstanceOf(None::class, (new Some(3))->filter($isEven));
        self::assertInstanceOf(Some::class, (new Some(4))->filter($isEven));
    }

    public function testOptionOr(): void
    {
        $some02 = new Some(2);
        $some100 = new Some(100);
        $none = new None();

        self::assertInstanceOf(Some::class, $some02->or($none));
        self::assertSame(2, $some02->or($none)->unwrap());

        self::assertInstanceOf(Some::class, $none->or($some100));
        self::assertSame(100, $none->or($some100)->unwrap());

        self::assertInstanceOf(Some::class, $some02->or($some100));
        self::assertSame(2, $some02->or($some100)->unwrap());

        self::assertInstanceOf(None::class, $none->or($none));
    }

    public function testOptionOrElse(): void
    {
        $nobody = static fn(): None => new None();
        $vikings = static fn(): Some => new Some('vikings');

        $option = (new Some('barbarians'))->orElse($vikings);
        self::assertInstanceOf(Some::class, $option);
        self::assertSame('barbarians', $option->unwrap());

        $option = (new None())->orElse($vikings);
        self::assertInstanceOf(Some::class, $option);
        self::assertSame('vikings', $option->unwrap());

        self::assertInstanceOf(None::class, (new None())->orElse($nobody));
    }

    public function testOptionXor(): void
    {
        $some = new Some(2);
        $none = new None();

        self::assertInstanceOf(Some::class, $some->xor($none));
        self::assertSame(2, $some->xor($none)->unwrap());

        self::assertInstanceOf(Some::class, $none->xor($some));
        self::assertSame(2, $none->xor($some)->unwrap());

        self::assertInstanceOf(None::class, $some->xor($some));
        self::assertInstanceOf(None::class, $none->xor($none));
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
