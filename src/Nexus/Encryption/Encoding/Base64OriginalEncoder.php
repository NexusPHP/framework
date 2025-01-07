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

namespace Nexus\Encryption\Encoding;

/**
 * Standard (A-Za-z0-9/\+) Base64 encoder.
 */
final class Base64OriginalEncoder extends AbstractBase64Encoder
{
    protected function getBase64Variant(): int
    {
        return SODIUM_BASE64_VARIANT_ORIGINAL;
    }
}
