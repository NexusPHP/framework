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
use Nexus\Password\Hash\BcryptHash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractHash::class)]
#[Group('unit-test')]
final class AbstractHashTest extends TestCase
{
    #[DataProvider('provideInvalidPasswordCases')]
    public function testInvalidPassword(string $password, bool $result): void
    {
        self::assertSame(
            $result,
            (new BcryptHash(Algorithm::Bcrypt))->isValidPassword($password),
        );
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function provideInvalidPasswordCases(): iterable
    {
        yield 'empty password' => ['', false];

        yield 'nul byte' => ["pass\0word", false];

        yield 'less than 8' => ['pass', false];

        yield 'max 4096' => [str_repeat('a', 4098), false];
    }
}
