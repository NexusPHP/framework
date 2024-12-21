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

use Nexus\Password\Algorithm;
use Nexus\Password\Hash\AbstractHash;
use Nexus\Password\Hash\SodiumHash;
use Nexus\Password\HashException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractHash::class)]
#[CoversClass(SodiumHash::class)]
#[Group('unit-test')]
final class SodiumHashTest extends TestCase
{
    public function testInvalidOpsLimitOnConstructor(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Operations limit should be 2 or greater, 1 given.');

        new SodiumHash(Algorithm::Sodium, ['opslimit' => 1]);
    }

    public function testInvalidMemLimitOnConstructor(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Memory limit should be 64MiB or greater (expressed in bytes), 10MiB given.');

        new SodiumHash(Algorithm::Sodium, ['memlimit' => 10 * 1024 ** 2]);
    }

    public function testInvalidAlgorithm(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Algorithm expected to be Algorithm::Sodium, Algorithm::Argon2id given.');

        new SodiumHash(Algorithm::Argon2id);
    }

    public function testBasicPasswordHashing(): void
    {
        $password = 'my-awesome-password';
        $hasher = new SodiumHash(Algorithm::Sodium);

        self::assertTrue($hasher->valid());

        $hash = $hasher->hash($password);
        self::assertFalse($hasher->needsRehash($hash));
    }

    #[DataProvider('provideInvalidPasswordForHashCases')]
    public function testInvalidPasswordForHash(string $password): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Invalid password provided.');

        (new SodiumHash(Algorithm::Sodium))->hash($password);
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

    public function testNeedsRehashing(): void
    {
        $hasher = new SodiumHash(Algorithm::Sodium);
        $hash = $hasher->hash('password');

        self::assertFalse($hasher->needsRehash($hash));
        self::assertTrue($hasher->needsRehash($hash, ['opslimit' => 2]));
        self::assertTrue($hasher->needsRehash($hash, ['memlimit' => 64 * 1024 ** 2]));
    }

    #[DataProvider('provideInvalidPasswordForVerifyCases')]
    public function testInvalidPasswordForVerify(bool $result, ?string $password, string $hash): void
    {
        $password ??= 'password';
        $hasher = new SodiumHash(Algorithm::Sodium);

        self::assertSame($result, $hasher->verify($password, $hash));
    }

    /**
     * @return iterable<string, array{bool, null|string, string}>
     */
    public static function provideInvalidPasswordForVerifyCases(): iterable
    {
        $hash = (new SodiumHash(Algorithm::Sodium))->hash('password');

        yield 'empty' => [false, '', $hash];

        yield 'nul' => [false, "pass\0word", $hash];

        yield 'short' => [false, 'aa', $hash];

        yield 'very long' => [false, str_repeat('a', 4098), $hash];

        yield 'corrupted' => [false, null, str_replace(SODIUM_CRYPTO_PWHASH_STRPREFIX, '$2y$', $hash)];

        yield 'valid hash' => [true, null, $hash];
    }
}
