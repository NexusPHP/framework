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

namespace Nexus\Tests\Password\Hash;

use Nexus\Password\Hash\AbstractArgon2Hash;
use Nexus\Password\HashException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
abstract class AbstractArgon2HashTestCase extends TestCase
{
    public function testInvalidMemoryCost(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Memory cost should be 7KiB or greater, 5KiB given.');

        static::argonHash(['memory_cost' => 5 * 1024]);
    }

    public function testInvalidTimeCost(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Time cost should be 1 or greater, 0 given.');

        static::argonHash(['time_cost' => 0]);
    }

    public function testInvalidThreads(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Number of threads should be 1 or greater, 0 given.');

        static::argonHash(['threads' => 0]);
    }

    public function testBasicPasswordHashing(): void
    {
        $password = 'my-awesome-password';
        $hasher = static::argonHash();

        $hash = $hasher->hash($password);
        self::assertTrue($hasher->verify($password, $hash));
    }

    /**
     * @param array{
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $option
     */
    #[DataProvider('providePasswordNeedsRehashCases')]
    public function testPasswordNeedsRehash(array $option): void
    {
        $password = 'myPassword';
        $hasher = static::argonHash();

        $hash = $hasher->hash($password);
        self::assertFalse($hasher->needsRehash($hash));
        self::assertTrue($hasher->needsRehash($hash, $option));
    }

    /**
     * @return iterable<string, array{array<string, int>}>
     */
    public static function providePasswordNeedsRehashCases(): iterable
    {
        yield 'memory cost' => [['memory_cost' => 256 * 1024]];

        yield 'time cost' => [['time_cost' => 5]];

        yield 'threads' => [['threads' => 3]];
    }

    #[DataProvider('provideInvalidPasswordForHashCases')]
    public function testInvalidPasswordPassedToHash(string $password): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Invalid password provided.');

        static::argonHash()->hash($password);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidPasswordForHashCases(): iterable
    {
        yield 'empty' => [''];

        yield 'nul' => ["pass\0word"];

        yield 'short' => ['pass'];

        yield 'very long' => [str_repeat('a', 4098)];
    }

    #[DataProvider('provideInvalidPasswordForVerifyCases')]
    public function testInvalidPasswordForVerify(bool $result, ?string $password, string $hash): void
    {
        $password ??= 'password';
        $hasher = static::argonHash();

        self::assertSame($result, $hasher->verify($password, $hash));
    }

    /**
     * @return iterable<string, array{bool, null|string, string}>
     */
    public static function provideInvalidPasswordForVerifyCases(): iterable
    {
        $hash = static::argonHash()->hash('password');

        yield 'empty' => [false, '', $hash];

        yield 'nul' => [false, "pass\0word", $hash];

        yield 'short' => [false, 'aa', $hash];

        yield 'very long' => [false, str_repeat('a', 4098), $hash];

        yield 'valid hash' => [true, null, $hash];
    }

    public function testIsValidHash(): void
    {
        self::assertTrue(static::argonHash()->valid());
    }

    /**
     * @param array{
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     */
    abstract protected static function argonHash(array $options = []): AbstractArgon2Hash;
}
