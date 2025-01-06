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
 * An object representation for a secret. This encapsulates sensitive strings
 * in a read-only object that forbids serialisation and dumping.
 *
 * This should be used for:
 * - secret keys
 * - plaintext (before encryption)
 * - plaintext (after decryption)
 */
final class Secret
{
    /**
     * @throws \RuntimeException
     */
    public function __construct(
        #[\SensitiveParameter]
        private ?string $value,
    ) {
        if (null === $this->value) {
            throw new \RuntimeException('Secret cannot accept null.');
        }
    }

    public function __destruct()
    {
        sodium_memzero($this->value);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function __serialize(): never
    {
        throw new \BadMethodCallException('Cannot serialise a Secret object.');
    }

    /**
     * @return array{value: string}
     */
    public function __debugInfo(): array
    {
        return ['value' => '[redacted]'];
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->reveal(), $other->reveal());
    }

    public function reveal(): string
    {
        return $this->value ?? '';
    }
}
