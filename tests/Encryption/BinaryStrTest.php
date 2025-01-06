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

use Nexus\Encryption\BinaryStr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BinaryStr::class)]
#[Group('unit-test')]
final class BinaryStrTest extends TestCase
{
    public function testStrlen(): void
    {
        $binaryStr = str_repeat("\x00", 10);
        self::assertSame(10, BinaryStr::strlen($binaryStr));
    }

    public function testSubstr(): void
    {
        $binaryStr = str_repeat("\x00", 10);
        self::assertSame("\x00\x00", BinaryStr::substr($binaryStr, 0, 2));
    }
}
