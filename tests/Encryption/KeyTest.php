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

use Nexus\Encryption\EncoderInterface;
use Nexus\Encryption\Key;
use Nexus\Encryption\Util;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Key::class)]
#[Group('unit-test')]
final class KeyTest extends TestCase
{
    private EncoderInterface&MockObject $encoder;

    protected function setUp(): void
    {
        $this->encoder = $this->createMock(EncoderInterface::class);
    }

    public function testKeyArmor(): void
    {
        $key = Util::secureRandom(32);
        $keyObj = new Key($key, $this->encoder);
        $this->encoder->expects(self::once())
            ->method('bin2hex')
            ->with($key)
            ->willReturn(bin2hex($key))
        ;

        self::assertTrue(ctype_xdigit($keyObj->armor()));
    }

    public function testKeyUnarmor(): void
    {
        $key = Util::secureRandom(32);
        $keyObj = new Key($key, $this->encoder);
        $this->encoder->expects(self::once())
            ->method('hex2bin')
            ->with(bin2hex($key))
            ->willReturn($key)
        ;

        self::assertSame($key, $keyObj->unarmor(bin2hex($key))->raw());
    }
}
