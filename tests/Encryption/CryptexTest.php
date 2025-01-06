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

use Nexus\Encryption\Cryptex;
use Nexus\Encryption\Encoding\Base64OriginalEncoder;
use Nexus\Encryption\Encoding\Base64OriginalNoPaddingEncoder;
use Nexus\Encryption\Encoding\Base64UrlSafeEncoder;
use Nexus\Encryption\Encoding\Base64UrlSafeNoPaddingEncoder;
use Nexus\Encryption\Encoding\EncoderInterface;
use Nexus\Encryption\Encoding\HexEncoder;
use Nexus\Encryption\Encoding\NullEncoder;
use Nexus\Encryption\Exception\InvalidEncodingVariantException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Cryptex::class)]
#[Group('unit-test')]
final class CryptexTest extends TestCase
{
    public function testInvalidVariantThrows(): void
    {
        $this->expectException(InvalidEncodingVariantException::class);
        $this->expectExceptionMessage('Unknown variant for encoder.');

        Cryptex::encoder(10);
    }

    /**
     * @param class-string<EncoderInterface> $expectedEncoder
     */
    #[DataProvider('provideEncoderCases')]
    public function testEncoder(int $variant, string $expectedEncoder): void
    {
        self::assertInstanceOf($expectedEncoder, Cryptex::encoder($variant));
    }

    /**
     * @return iterable<string, array{int, class-string<EncoderInterface>}>
     */
    public static function provideEncoderCases(): iterable
    {
        yield 'base64 original' => [Cryptex::ENCODE_BASE64_ORIGINAL, Base64OriginalEncoder::class];

        yield 'base 64 original no padding' => [Cryptex::ENCODE_BASE64_ORIGINAL_NO_PADDING, Base64OriginalNoPaddingEncoder::class];

        yield 'base 64 URL safe' => [Cryptex::ENCODE_BASE64_URL_SAFE, Base64UrlSafeEncoder::class];

        yield 'base 64 URL safe no padding' => [Cryptex::ENCODE_BASE64_URL_SAFE_NO_PADDING, Base64UrlSafeNoPaddingEncoder::class];

        yield 'hex' => [Cryptex::ENCODE_HEX, HexEncoder::class];

        yield 'none' => [Cryptex::ENCODE_NONE, NullEncoder::class];
    }
}
