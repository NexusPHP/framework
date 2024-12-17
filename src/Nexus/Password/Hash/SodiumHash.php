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

namespace Nexus\Password\Hash;

use Nexus\Password\Algorithm;
use Nexus\Password\HashException;

final class SodiumHash extends AbstractHash
{
    /**
     * Represents a maximum amount of computations to perform.
     *
     * Raising this number will make the function require more CPU cycles to compute a key.
     * There are constants available to set the operations limit to appropriate values
     * depending on intended use, in order of strength:
     * - `SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE`
     * - `SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE`
     * - `SODIUM_CRYPTO_PWHASH_OPSLIMIT_SENSITIVE`
     */
    private int $opslimit;

    /**
     * The maximum amount of RAM that the function will use, in bytes.
     *
     * There are constants to help you choose an appropriate value, in order of size:
     * - `SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE`
     * - `SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE`
     * - `SODIUM_CRYPTO_PWHASH_MEMLIMIT_SENSITIVE`
     */
    private int $memlimit;

    /**
     * @param array{opslimit?: int, memlimit?: int} $options
     */
    public function __construct(
        public readonly Algorithm $algorithm,
        array $options = [],
    ) {
        $opslimit = $options['opslimit'] ?? SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE;
        $memlimit = $options['memlimit'] ?? SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE;

        if ($opslimit < SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE) {
            throw new HashException(\sprintf(
                'Operations limit should be %d or greater, %d given.',
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                $opslimit,
            ));
        }

        if ($memlimit < SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE) {
            throw new HashException(\sprintf(
                'Memory limit should be %sMiB or greater (expressed in bytes), %sMiB given.',
                number_format(SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE / 1024 ** 2),
                number_format($memlimit / 1024 ** 2),
            ));
        }

        $this->opslimit = $opslimit;
        $this->memlimit = $memlimit;
    }

    public function hash(#[\SensitiveParameter] string $password, array $options = []): string
    {
        if (! $this->isValidPassword($password)) {
            throw new HashException('Invalid password provided.');
        }

        return sodium_crypto_pwhash_str($password, ...$this->options($options));
    }

    public function needsRehash(string $hash, array $options = []): bool
    {
        return sodium_crypto_pwhash_str_needs_rehash($hash, ...$this->options($options));
    }

    public function verify(#[\SensitiveParameter] string $password, string $hash): bool
    {
        if (! $this->isValidPassword($password)) {
            return false;
        }

        if (! str_starts_with($hash, SODIUM_CRYPTO_PWHASH_STRPREFIX)) {
            return false;
        }

        return sodium_crypto_pwhash_str_verify($hash, $password);
    }

    public function valid(): bool
    {
        return \extension_loaded('sodium')
            && version_compare(SODIUM_LIBRARY_VERSION, '1.0.14', '>=');
    }

    /**
     * @param array{opslimit?: int, memlimit?: int} $options
     *
     * @return array{opslimit: int, memlimit: int}
     */
    private function options(array $options): array
    {
        return [
            'opslimit' => $options['opslimit'] ?? $this->opslimit,
            'memlimit' => $options['memlimit'] ?? $this->memlimit,
        ];
    }
}
