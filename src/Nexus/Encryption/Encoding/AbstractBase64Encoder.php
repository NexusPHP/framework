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

abstract class AbstractBase64Encoder implements EncoderInterface
{
    public function encode(#[\SensitiveParameter] string $binaryString): string
    {
        return sodium_bin2base64($binaryString, $this->getBase64Variant());
    }

    public function decode(#[\SensitiveParameter] string $encodedString, string $ignore = ''): string
    {
        return sodium_base642bin($encodedString, $this->getBase64Variant(), $ignore);
    }

    /**
     * Get the variant of Base64 encoding to use.
     */
    abstract protected function getBase64Variant(): int;
}
