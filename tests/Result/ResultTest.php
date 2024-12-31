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

namespace Nexus\Tests\Result;

use Nexus\Result\Err;
use Nexus\Result\Ok;
use Nexus\Result\Result;
use Nexus\Result\UnwrappedResultException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Ok::class)]
#[CoversClass(Err::class)]
#[Group('unit-test')]
final class ResultTest extends TestCase
{
    public function testResultIsOk(): void
    {
        self::assertTrue((new Ok(-3))->isOk());
        self::assertFalse((new Err('some error'))->isOk());
    }

    public function testResultIsOkAnd(): void
    {
        $predicate = static fn(int $v): bool => $v > 1;

        self::assertTrue((new Ok(2))->isOkAnd($predicate));
        self::assertFalse((new Ok(0))->isOkAnd($predicate));
        self::assertFalse((new Err('hey'))->isOkAnd($predicate));
    }

    public function testResultIsErr(): void
    {
        self::assertFalse((new Ok(-3))->isErr());
        self::assertTrue((new Err('some err'))->isErr());
    }

    public function testResultIsErrAnd(): void
    {
        $predicate = static fn(string $v): bool => \strlen($v) > 4;

        self::assertFalse((new Ok('heart'))->isErrAnd($predicate));
        self::assertFalse((new Err('hey'))->isErrAnd($predicate));
        self::assertTrue((new Err('heart'))->isErrAnd($predicate));
    }

    public function testResultMap(): void
    {
        $predicate = static fn(string $v): int => (int) $v * 2;
        $err = new Err('some error');

        self::assertSame(2, (new Ok('1'))->map($predicate)->unwrap());
        self::assertSame($err, $err->map($predicate));
    }

    public function testResultMapOr(): void
    {
        $predicate = static fn(string $v): int => \strlen($v);
        $default = 42;

        self::assertSame(3, (new Ok('foo'))->mapOr($default, $predicate));
        self::assertSame($default, (new Err('bar'))->mapOr($default, $predicate));
    }

    public function testResultMapOrElse(): void
    {
        $default = static fn(string $e): int => 42;
        $predicate = static fn(string $v): int => \strlen($v);

        self::assertSame(3, (new Ok('foo'))->mapOrElse($default, $predicate));
        self::assertSame(42, (new Err('bar'))->mapOrElse($default, $predicate));
    }

    public function testResultMapErr(): void
    {
        $predicate = static fn(int $code): string => \sprintf('error code: %d', $code);
        $ok = new Ok(2);

        self::assertSame($ok, $ok->mapErr($predicate));
        self::assertSame('error code: 500', (new Err(500))->mapErr($predicate)->unwrapErr());
    }

    #[DataProvider('provideResultUnwrapCases')]
    public function testResultUnwrap(mixed $err, string $message): void
    {
        self::assertSame(2, (new Ok(2))->unwrap());

        $this->expectException(UnwrappedResultException::class);
        $this->expectExceptionMessage($message);
        (new Err($err))->unwrap();
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function provideResultUnwrapCases(): iterable
    {
        yield 'exception' => [new \RuntimeException('hello'), 'Unwrapped an Err result: hello'];

        yield 'error' => [new \Error('error message'), 'Unwrapped an Err result: error message'];

        yield 'string' => ['string message', 'Unwrapped an Err result: \'string message\''];

        yield 'int' => [42, 'Unwrapped an Err result: 42'];

        yield 'float' => [200.0, 'Unwrapped an Err result: 200.0'];

        yield 'true' => [true, 'Unwrapped an Err result: true'];

        yield 'false' => [false, 'Unwrapped an Err result: false'];

        yield 'array' => [[], 'Unwrapped an Err result.'];

        yield 'class' => [[], 'Unwrapped an Err result.'];
    }

    public function testResultUnwrapOr(): void
    {
        $default = 2;

        self::assertSame(9, (new Ok(9))->unwrapOr($default));
        self::assertSame($default, (new Err('error'))->unwrapOr($default));
    }

    public function testResultUnwrapOrElse(): void
    {
        $predicate = static fn(string $v): int => \strlen($v);

        self::assertSame(2, (new Ok(2))->unwrapOrElse($predicate));
        self::assertSame(3, (new Err('foo'))->unwrapOrElse($predicate));
    }

    #[DataProvider('provideResultUnwrapErrCases')]
    public function testResultUnwrapErr(mixed $value, string $message): void
    {
        self::assertSame('hey', (new Err('hey'))->unwrapErr());

        $this->expectException(UnwrappedResultException::class);
        $this->expectExceptionMessage($message);
        (new Ok($value))->unwrapErr();
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function provideResultUnwrapErrCases(): iterable
    {
        yield 'exception' => [new \RuntimeException('hello'), 'Unwrapped an Ok result: hello'];

        yield 'error' => [new \Error('message'), 'Unwrapped an Ok result: message'];

        yield 'string' => ['string message', 'Unwrapped an Ok result: \'string message\''];

        yield 'int' => [42, 'Unwrapped an Ok result: 42'];

        yield 'float' => [200.0, 'Unwrapped an Ok result: 200.0'];

        yield 'true' => [true, 'Unwrapped an Ok result: true'];

        yield 'false' => [false, 'Unwrapped an Ok result: false'];

        yield 'array' => [[], 'Unwrapped an Ok result.'];

        yield 'class' => [[], 'Unwrapped an Ok result.'];
    }

    public function testResultAnd(): void
    {
        $x = new Ok(2);
        $y = new Err('late error');
        self::assertSame($y, $x->and($y));

        $x = new Err('early error');
        $y = new Ok('foo');
        self::assertSame($x, $x->and($y));

        $x = new Err('not a 2');
        $y = new Err('late error');
        self::assertSame($x, $x->and($y));

        $x = new Ok(2);
        $y = new Ok('different result type');
        self::assertSame($y, $x->and($y));
    }

    public function testResultAndThen(): void
    {
        $squareThenToString = static function (int $v): Result {
            try {
                return new Ok((string) ($v ** 2 / $v));
            } catch (\Throwable $e) {
                return new Err($e->getMessage());
            }
        };

        self::assertSame('2', (new Ok(2))->andThen($squareThenToString)->unwrap());
        self::assertSame('Division by zero', (new Ok(0))->andThen($squareThenToString)->unwrapErr());
        self::assertSame('NaN', (new Err('NaN'))->andThen($squareThenToString)->unwrapErr());
    }

    public function testResultOr(): void
    {
        $x = new Ok(2);
        $y = new Err('late error');
        self::assertSame($x, $x->or($y));

        $x = new Err('early error');
        $y = new Ok('foo');
        self::assertSame($y, $x->or($y));

        $x = new Err('not a 2');
        $y = new Err('late error');
        self::assertSame($y, $x->or($y));

        $x = new Ok(2);
        $y = new Ok(100);
        self::assertSame($x, $x->or($y));
    }

    public function testResultOrElse(): void
    {
        $sq = static fn(int $x): Result => new Ok($x * $x);
        $err = static fn(int $x): Result => new Err($x);

        self::assertTrue((new Ok(2))->orElse($sq)->orElse($sq)->isOk());
        self::assertTrue((new Ok(2))->orElse($err)->orElse($sq)->isOk());
        self::assertTrue((new Err(3))->orElse($sq)->orElse($err)->isOk());
        self::assertTrue((new Err(3))->orElse($err)->orElse($err)->isErr());
    }
}
