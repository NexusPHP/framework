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

final readonly class Secret
{
    public function __construct(
        #[\SensitiveParameter]
        private string $key,
        private EncoderInterface $encoder,
    ) {}

    /**
     * Armor the secret in an ASCII format.
     */
    public function armor(): string
    {
        return $this->encoder->bin2hex($this->key);
    }

    /**
     * Unarmor the secret from an ASCII format to its raw binary form.
     */
    public function unarmor(string $key): self
    {
        return new self($this->encoder->hex2bin($key), $this->encoder);
    }

    /**
     * Get the raw binary secret.
     */
    public function raw(): string
    {
        return $this->key;
    }
}
