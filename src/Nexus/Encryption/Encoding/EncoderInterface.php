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

interface EncoderInterface
{
    /**
     * Encodes a binary string into a readable format without cache-timing attacks.
     */
    public function encode(#[\SensitiveParameter] string $binaryString): string;

    /**
     * Converts an encoded string back to its original binary format without cache-timing attacks.
     *
     * @param string $ignore Characters to ignore when decoding (e.g. whitespace characters)
     */
    public function decode(#[\SensitiveParameter] string $encodedString, string $ignore = ''): string;
}
