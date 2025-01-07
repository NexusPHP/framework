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
use Nexus\Encryption\Encoding\NullEncoder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 */
#[CoversClass(NullEncoder::class)]
#[Group('unit-test')]
final class NullEncoderTest extends AbstractEncoderTestCase
{
    protected function createEncoder(): EncoderInterface
    {
        return new NullEncoder();
    }

    protected function nativeEncode(string $data): string
    {
        return $data;
    }

    protected function nativeDecode(string $encoded): string
    {
        return $encoded;
    }
}
