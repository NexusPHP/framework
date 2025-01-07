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

abstract class Key
{
    private ?string $keyString;

    public function __construct(Secret $key)
    {
        $this->keyString = $key->reveal();
    }

    public function __destruct()
    {
        sodium_memzero($this->keyString);
    }

    public function __clone(): void
    {
        throw new \BadMethodCallException(\sprintf('Cannot clone a %s object.', basename(static::class)));
    }

    /**
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['keyString' => '[redacted]'];
    }

    public function __serialize(): never
    {
        throw new \BadMethodCallException(\sprintf('Cannot serialise a %s object.', static::class));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): never
    {
        throw new \BadMethodCallException(\sprintf('Cannot unserialise a %s object.', static::class)); // @codeCoverageIgnore
    }

    public function getKeyString(): string
    {
        return $this->keyString ?? '';
    }
}
