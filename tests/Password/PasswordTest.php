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

namespace Nexus\Tests\Password;

use Nexus\Password\Algorithm;
use Nexus\Password\Hash\Argon2idHash;
use Nexus\Password\Hash\Argon2iHash;
use Nexus\Password\Hash\BcryptHash;
use Nexus\Password\Password;
use Nexus\PHPUnit\Tachycardia\Attribute\TimeLimit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Password::class)]
#[Group('unit-test')]
final class PasswordTest extends TestCase
{
    public function testFactory(): void
    {
        self::assertInstanceOf(BcryptHash::class, Password::fromAlgorithm(Algorithm::Bcrypt));
        self::assertInstanceOf(Argon2iHash::class, Password::fromAlgorithm(Algorithm::Argon2i));
        self::assertInstanceOf(Argon2idHash::class, Password::fromAlgorithm(Algorithm::Argon2id));
    }

    #[TimeLimit(3.0)]
    public function testGetInfoBcrypt(): void
    {
        self::assertSame([
            'algorithm' => Algorithm::Bcrypt,
            'options' => [
                'cost' => BcryptHash::DEFAULT_COST,
            ],
        ], Password::getInfo(
            Password::fromAlgorithm(Algorithm::Bcrypt)->hash('password'),
        ));
        self::assertSame([
            'algorithm' => Algorithm::Bcrypt,
            'options' => [
                'cost' => 15,
            ],
        ], Password::getInfo(
            Password::fromAlgorithm(Algorithm::Bcrypt, ['cost' => 15])->hash('password'),
        ));
    }

    public function testGetInfoArgon2i(): void
    {
        self::assertSame([
            'algorithm' => Algorithm::Argon2i,
            'options' => [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
            ],
        ], Password::getInfo(
            Password::fromAlgorithm(Algorithm::Argon2i)->hash('password'),
        ));
        self::assertSame([
            'algorithm' => Algorithm::Argon2i,
            'options' => [
                'memory_cost' => 128 * 1024,
                'time_cost' => 5,
                'threads' => 2,
            ],
        ], Password::getInfo(
            Password::fromAlgorithm(Algorithm::Argon2i, [
                'memory_cost' => 128 * 1024,
                'time_cost' => 5,
                'threads' => 2,
            ])->hash('password'),
        ));
    }

    public function testGetInfoArgon2id(): void
    {
        self::assertSame([
            'algorithm' => Algorithm::Argon2id,
            'options' => [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
            ],
        ], Password::getInfo(
            Password::fromAlgorithm(Algorithm::Argon2id)->hash('password'),
        ));
        self::assertSame([
            'algorithm' => Algorithm::Argon2id,
            'options' => [
                'memory_cost' => 128 * 1024,
                'time_cost' => 5,
                'threads' => 2,
            ],
        ], Password::getInfo(
            Password::fromAlgorithm(Algorithm::Argon2id, [
                'memory_cost' => 128 * 1024,
                'time_cost' => 5,
                'threads' => 2,
            ])->hash('password'),
        ));
    }

    public function testInvalidHashThrowsOnGetInfo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid hash provided.');

        Password::getInfo('gibberish');
    }
}
