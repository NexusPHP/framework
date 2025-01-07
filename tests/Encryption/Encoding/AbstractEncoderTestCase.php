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

namespace Nexus\Tests\Encryption\Encoding;

use Nexus\Encryption\Encoding\EncoderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
abstract class AbstractEncoderTestCase extends TestCase
{
    /**
     * @param int<1, 32> $i
     */
    #[DataProvider('provideEncodeDecodeCases')]
    public function testEncodeDecode(int $i): void
    {
        $encoder = $this->createEncoder();
        $data = random_bytes($i);

        $encoded = $encoder->encode($data);
        $decoded = $encoder->decode($encoded);

        self::assertSame($data, $decoded);
        self::assertSame($this->nativeEncode($data), $encoded);
        self::assertSame($this->nativeDecode($encoded), $decoded);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function provideEncodeDecodeCases(): iterable
    {
        for ($i = 1; $i <= 32; ++$i) {
            yield \sprintf('%02d bytes', $i) => [$i];
        }
    }

    abstract protected function createEncoder(): EncoderInterface;

    abstract protected function nativeEncode(string $data): string;

    abstract protected function nativeDecode(string $encoded): string;
}
