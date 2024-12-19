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
use Nexus\Password\Hash\Pbkdf2Hash;
use Nexus\Password\HashException;
use Nexus\Password\Password;
use Nexus\PHPUnit\Tachycardia\Attribute\TimeLimit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractHash::class)]
#[CoversClass(Pbkdf2Hash::class)]
#[Group('unit-test')]
final class Pbkdf2HashTest extends TestCase
{
    public function testInvalidAlgorithm(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Algorithm expected to be any of Algorith::Pbkdf2HmacSha1, Algorith::Pbkdf2HmacSha256, Algorith::Pbkdf2HmacSha512, but Algorithm::Argon2i given.');

        new Pbkdf2Hash(Algorithm::Argon2i);
    }

    public function testInvalidIterations(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Internal iterations expected to be 1,000 or greater, 900 given.');

        new Pbkdf2Hash(Algorithm::Pbkdf2HmacSha256, ['iterations' => 900]);
    }

    public function testInvalidLength(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Length of the output string expected to be 0 or greater, -1 given.');

        new Pbkdf2Hash(Algorithm::Pbkdf2HmacSha512, ['length' => -1]);
    }

    #[DataProvider('provideDefaultIterationsCases')]
    public function testDefaultIterations(Algorithm $algorithm, int $iterations): void
    {
        $pbkdf2 = new Pbkdf2Hash($algorithm);
        $rm = new \ReflectionMethod($pbkdf2, 'defaultIterations');

        self::assertSame($iterations, $rm->invoke($pbkdf2));
    }

    /**
     * @return iterable<string, array{Algorithm, int}>
     */
    public static function provideDefaultIterationsCases(): iterable
    {
        yield 'sha1' => [Algorithm::Pbkdf2HmacSha1, 1_300_000];

        yield 'sha256' => [Algorithm::Pbkdf2HmacSha256, 600_000];

        yield 'sha512' => [Algorithm::Pbkdf2HmacSha512, 210_000];
    }

    #[DataProvider('provideLengthProvidedCorrespondsToHashLengthCases')]
    public function testLengthProvidedCorrespondsToHashLength(int $result, int $length, string $algo): void
    {
        $algorithm = Algorithm::from($algo);
        $hasher = new Pbkdf2Hash($algorithm, compact('length'));
        $hash = $hasher->hash('password');

        self::assertSame($result, \strlen($hash));
    }

    /**
     * @return iterable<string, array{int, int, string}>
     */
    public static function provideLengthProvidedCorrespondsToHashLengthCases(): iterable
    {
        yield 'sha1 0' => [40, 0, 'sha1'];

        yield 'sha256 0' => [64, 0, 'sha256'];

        yield 'sha512 0' => [128, 0, 'sha512'];

        foreach (['sha1', 'sha256', 'sha512'] as $algo) {
            yield $algo.' 30' => [30, 30, $algo];
        }
    }

    #[TimeLimit(3.0)]
    public function testBasicPasswordHashing(): void
    {
        $password = 'my-awesome-password';
        $salt = random_bytes(32);

        $hasher = new Pbkdf2Hash(Algorithm::Pbkdf2HmacSha256);
        self::assertTrue($hasher->valid());

        $hash = $hasher->hash($password, salt: $salt);
        self::assertFalse($hasher->needsRehash($hash));
        self::assertTrue($hasher->verify($password, $hash, $salt));
    }

    public function testInvalidPasswordForHash(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Invalid password provided.');

        (new Pbkdf2Hash(Algorithm::Pbkdf2HmacSha256))->hash('aa');
    }

    #[TimeLimit(3.0)]
    public function testPasswordVerify(): void
    {
        $pass1 = "abcd\0e";
        $pass2 = 'password';
        $pass3 = 'pass';
        $salt = random_bytes(16);
        $hasher = new Pbkdf2Hash(Algorithm::Pbkdf2HmacSha256);

        $hash = $hasher->hash($pass2, salt: $salt);
        self::assertFalse($hasher->verify($pass1, $hash, $salt));
        self::assertTrue($hasher->verify($pass2, $hash, $salt));
        self::assertFalse($hasher->verify($pass3, $hash, $salt));
        self::assertFalse($hasher->verify(
            $pass2,
            substr(Password::fromAlgorithm(Algorithm::Argon2i)->hash($pass2), 0, 40),
        ));
        self::assertFalse($hasher->verify(
            $pass2,
            (new Pbkdf2Hash(Algorithm::Pbkdf2HmacSha256, ['length' => 0]))->hash($pass2, salt: $salt),
        ));
    }
}
