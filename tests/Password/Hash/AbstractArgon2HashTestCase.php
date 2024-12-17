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

        $this->argonHash(['memory_cost' => 5 * 1024]);
    }

    public function testInvalidTimeCost(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Time cost should be 1 or greater, 0 given.');

        $this->argonHash(['time_cost' => 0]);
    }

    public function testInvalidThreads(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Number of threads should be 1 or greater, 0 given.');

        $this->argonHash(['threads' => 0]);
    }

    public function testBasicPasswordHashing(): void
    {
        $password = 'my-awesome-password';
        $hasher = $this->argonHash();

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
        $hasher = $this->argonHash();

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

    public function testInvalidPasswordPassedToHash(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Invalid password provided.');

        $this->argonHash()->hash("pass\0word");
    }

    public function testInvalidPasswordForVerify(): void
    {
        $pass1 = "abcd\0e";
        $pass2 = str_repeat('a', 4098);
        $pass3 = 'password';
        $pass4 = 'pass';
        $hasher = $this->argonHash();

        $hash = $hasher->hash($pass3);
        self::assertFalse($hasher->verify($pass1, $hash));
        self::assertFalse($hasher->verify($pass2, $hash));
        self::assertTrue($hasher->verify($pass3, $hash));
        self::assertFalse($hasher->verify($pass4, $hash));
    }

    public function testIsValidHash(): void
    {
        self::assertTrue($this->argonHash()->valid());
    }

    /**
     * @param array{
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     */
    abstract protected function argonHash(array $options = []): AbstractArgon2Hash;
}
