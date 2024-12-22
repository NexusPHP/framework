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

namespace Nexus\Tests\Encryption;

use Nexus\Encryption\Util;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Util::class)]
#[Group('unit-test')]
final class UtilTest extends TestCase
{
    public function testSecureRandomPositiveInt(): void
    {
        $bytes = Util::secureRandom(16);
        self::assertSame(16, Util::strlen($bytes));
    }

    #[DataProvider('provideSecureRandomInvalidLengthCases')]
    public function testSecureRandomInvalidLength(int $length): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Length must be greater than zero.');
        Util::secureRandom($length);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function provideSecureRandomInvalidLengthCases(): iterable
    {
        yield 'negative int' => [-1];

        yield 'zero' => [0];
    }

    public function testSubstr(): void
    {
        $key = Util::secureRandom(32);
        $nonce = Util::secureRandom(24);
        $combined = $nonce.$key;

        self::assertSame($nonce, Util::substr($combined, 0, 24));
        self::assertSame($key, Util::substr($combined, 24));
    }

    public function testStrlen(): void
    {
        self::assertSame(32, Util::strlen(Util::secureRandom(32)));
        self::assertSame(3, Util::strlen("\x00\x80\xFF"));
    }
}
