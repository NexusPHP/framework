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
use Nexus\Password\Password;
use Nexus\PHPUnit\Tachycardia\Attribute\TimeLimit;
use PHPUnit\Framework\Attributes\CoversClass;
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
        self::assertTrue($hasher->verify($password, $hash));
    }

    public function testInvalidPasswordForHash(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Invalid password provided.');

        (new SodiumHash(Algorithm::Sodium))->hash('aa');
    }

    public function testNeedsRehashing(): void
    {
        $hasher = new SodiumHash(Algorithm::Sodium);
        $hash = $hasher->hash('password');

        self::assertFalse($hasher->needsRehash($hash));
        self::assertTrue($hasher->needsRehash($hash, ['opslimit' => 2]));
        self::assertTrue($hasher->needsRehash($hash, ['memlimit' => 64 * 1024 ** 2]));
    }

    #[TimeLimit(1.5)]
    public function testInvalidPasswordForVerify(): void
    {
        $pass1 = "abcd\0e";
        $pass2 = 'password';
        $pass3 = 'pass';
        $hasher = new SodiumHash(Algorithm::Sodium);

        $hash = $hasher->hash($pass2);
        self::assertFalse($hasher->verify($pass1, $hash));
        self::assertTrue($hasher->verify($pass2, $hash));
        self::assertFalse($hasher->verify($pass3, $hash));
        self::assertFalse($hasher->verify(
            $pass2,
            Password::fromAlgorithm(Algorithm::Bcrypt)->hash($pass2),
        ));
    }
}
