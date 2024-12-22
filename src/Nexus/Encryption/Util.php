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

final class Util
{
    /**
     * Generates cryptographically secure random bytes.
     */
    public static function secureRandom(int $length): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Length must be greater than zero.');
        }

        return random_bytes($length);
    }

    /**
     * Get a substring of a given string.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        // mb_substr() on 8bit encoding is the same as substr()
        return substr($string, $start, $length);
    }

    /**
     * Get the length of a given string.
     */
    public static function strlen(string $string): int
    {
        // mb_strlen() on 8bit encoding is the same as strlen()
        return \strlen($string);
    }
}
