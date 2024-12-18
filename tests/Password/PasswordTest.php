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
use Nexus\Password\Hash\Pbkdf2Hash;
use Nexus\Password\Hash\SodiumHash;
use Nexus\Password\Password;
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
        self::assertInstanceOf(Argon2iHash::class, Password::fromAlgorithm(Algorithm::Argon2i));
        self::assertInstanceOf(Argon2idHash::class, Password::fromAlgorithm(Algorithm::Argon2id));
        self::assertInstanceOf(BcryptHash::class, Password::fromAlgorithm(Algorithm::Bcrypt));
        self::assertInstanceOf(Pbkdf2Hash::class, Password::fromAlgorithm(Algorithm::Pbkdf2HmacSha1));
        self::assertInstanceOf(Pbkdf2Hash::class, Password::fromAlgorithm(Algorithm::Pbkdf2HmacSha256));
        self::assertInstanceOf(Pbkdf2Hash::class, Password::fromAlgorithm(Algorithm::Pbkdf2HmacSha512));
        self::assertInstanceOf(SodiumHash::class, Password::fromAlgorithm(Algorithm::Sodium));
    }
}
