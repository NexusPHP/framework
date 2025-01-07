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

final class NullEncoder implements EncoderInterface
{
    public function encode(#[\SensitiveParameter] string $binaryString): string
    {
        return $binaryString;
    }

    public function decode(#[\SensitiveParameter] string $encodedString, string $ignore = ''): string
    {
        return $encodedString;
    }
}
