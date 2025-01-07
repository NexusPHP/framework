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

use Nexus\Encryption\Key;
use Nexus\Encryption\Secret;
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
    private Key&MockObject $key;

    protected function setUp(): void
    {
        parent::setUp();

        $this->key = $this->getMockBuilder(Key::class)
            ->setConstructorArgs([new Secret(random_bytes(32))])
            ->onlyMethods([])
            ->getMock()
        ;
    }

    public function testCannotCloneKey(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(\sprintf('Cannot clone a %s object.', \get_class($this->key)));

        clone $this->key; // @phpstan-ignore expr.resultUnused
    }

    public function testCannotSerialiseKey(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(\sprintf('Cannot serialise a %s object.', \get_class($this->key)));

        serialize($this->key);
    }

    public function testHidesKeyStringFromDump(): void
    {
        $secret = new Secret(random_bytes(32));
        $this->key = $this->getMockBuilder(Key::class)
            ->setConstructorArgs([$secret])
            ->onlyMethods([])
            ->getMock()
        ;

        ob_start();
        var_dump($this->key);
        $dump = (string) ob_get_clean();
        $print = print_r($this->key, true);

        self::assertStringNotContainsString($secret->reveal(), $dump);
        self::assertStringContainsString('[redacted]', $dump);
        self::assertStringNotContainsString($secret->reveal(), $print);
        self::assertStringContainsString('[redacted]', $print);
    }

    public function testGetKeyString(): void
    {
        $secret = new Secret(random_bytes(32));
        $this->key = $this->getMockBuilder(Key::class)
            ->setConstructorArgs([$secret])
            ->onlyMethods([])
            ->getMock()
        ;

        self::assertSame($secret->reveal(), $this->key->getKeyString());
    }
}
