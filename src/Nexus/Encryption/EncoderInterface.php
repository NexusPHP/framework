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

interface EncoderInterface
{
    /**
     * Encodes binary data to hexadecimal representation.
     */
    public function bin2hex(string $data): string;

    /**
     * Decodes hexadecimal data to binary representation.
     */
    public function hex2bin(string $data): string;
}
