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
use Nexus\Password\Hash\AbstractArgon2Hash;
use Nexus\Password\Hash\AbstractHash;
use Nexus\Password\Hash\Argon2idHash;
use Nexus\Password\HashException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 */
#[CoversClass(AbstractArgon2Hash::class)]
#[CoversClass(Argon2idHash::class)]
#[CoversClass(AbstractHash::class)]
#[Group('unit-test')]
final class Argon2idHashTest extends AbstractArgon2HashTestCase
{
    public function testInvalidAlgorithm(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Algorithm expected to be Algorithm::Argon2id, Algorithm::Argon2i given.');

        new Argon2idHash(Algorithm::Argon2i);
    }

    protected static function argonHash(array $options = []): AbstractArgon2Hash
    {
        return new Argon2idHash(Algorithm::Argon2id, $options);
    }
}
