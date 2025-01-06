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

namespace Nexus\Tests\Encryption\Exception;

use Nexus\Encryption\Cryptex;
use Nexus\Encryption\Exception\InvalidEncodingVariantException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(InvalidEncodingVariantException::class)]
#[Group('unit-test')]
final class InvalidEncodingVariantExceptionTest extends TestCase
{
    public function testMessage(): void
    {
        try {
            Cryptex::encoder(10);
        } catch (InvalidEncodingVariantException $e) {
            self::assertSame('Unknown variant for encoder.', $e->getMessage());
        }
    }
}
