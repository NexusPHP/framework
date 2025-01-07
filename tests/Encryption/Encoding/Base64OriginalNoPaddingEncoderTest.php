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

use Nexus\Encryption\Encoding\AbstractBase64Encoder;
use Nexus\Encryption\Encoding\Base64OriginalNoPaddingEncoder;
use Nexus\Encryption\Encoding\EncoderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 */
#[CoversClass(AbstractBase64Encoder::class)]
#[CoversClass(Base64OriginalNoPaddingEncoder::class)]
#[Group('unit-test')]
final class Base64OriginalNoPaddingEncoderTest extends AbstractEncoderTestCase
{
    protected function createEncoder(): EncoderInterface
    {
        return new Base64OriginalNoPaddingEncoder();
    }

    protected function nativeEncode(string $data): string
    {
        return sodium_bin2base64($data, SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING);
    }

    protected function nativeDecode(string $encoded): string
    {
        return sodium_base642bin($encoded, SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING);
    }
}
