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

use Nexus\Encryption\Encoding\HexEncoder;
use Nexus\Encryption\Secret;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Secret::class)]
#[Group('unit-test')]
final class SecretTest extends TestCase
{
    private string $data;

    protected function setUp(): void
    {
        $this->data = (new HexEncoder())->encode(random_bytes(32));
    }

    public function testCannotAcceptNullOnConstruct(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Secret cannot accept null.');

        new Secret(null);
    }

    public function testCannotSerialise(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialise a Secret object.');

        serialize(new Secret($this->data));
    }

    public function testCannotDumpString(): void
    {
        $plaintext = new Secret($this->data);

        ob_start();
        var_dump($plaintext);
        $dump = (string) ob_get_clean();
        $dump = preg_replace('/(\033\[[0-9;]+m)|(\035\[[0-9;]+m)/u', '', $dump) ?? $dump;
        $print = print_r($plaintext, true);

        self::assertStringNotContainsString($this->data, $dump);
        self::assertStringContainsString('[redacted]', $dump);
        self::assertStringNotContainsString($this->data, $print);
        self::assertStringContainsString('[redacted]', $print);
    }

    public function testEquals(): void
    {
        $one = new Secret($this->data);
        $two = new Secret($this->data);
        $three = new Secret((new HexEncoder())->encode(random_bytes(32)));

        self::assertTrue($one->equals($two));
        self::assertFalse($one->equals($three));
        self::assertTrue($two->equals($one));
        self::assertFalse($two->equals($three));
        self::assertFalse($three->equals($one));
        self::assertFalse($three->equals($two));
    }

    public function testRevealNeverReturnsEmptyString(): void
    {
        $plaintext = new Secret($this->data);

        self::assertSame($this->data, $plaintext->reveal());
    }
}
