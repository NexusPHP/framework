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

namespace Nexus\Encryption;

/**
 * Binary string operations wrapping on related `mb_*` functions.
 */
final class BinaryStr
{
    /**
     * Get the length of a binary string.
     */
    public static function strlen(#[\SensitiveParameter] string $string): int
    {
        return mb_strlen($string, '8bit');
    }

    /**
     * Get a substring of a binary string.
     */
    public static function substr(#[\SensitiveParameter] string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, '8bit');
    }
}
