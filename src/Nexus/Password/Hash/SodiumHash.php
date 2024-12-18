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

final readonly class SodiumHash extends AbstractHash
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
     *
     * @var int<SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, max>
     */
    private int $opslimit;

    /**
     * The maximum amount of RAM that the function will use, in bytes.
     *
     * There are constants to help you choose an appropriate value, in order of size:
     * - `SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE`
     * - `SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE`
     * - `SODIUM_CRYPTO_PWHASH_MEMLIMIT_SENSITIVE`
     *
     * @var int<SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE, max>
     */
    private int $memlimit;

    /**
     * @param array{opslimit?: int, memlimit?: int} $options
     */
    public function __construct(
        public Algorithm $algorithm,
        array $options = [],
    ) {
        if (Algorithm::Sodium !== $algorithm) {
            throw new HashException(\sprintf(
                'Algorithm expected to be Algorithm::Sodium, Algorithm::%s given.',
                $algorithm->name,
            ));
        }

        ['opslimit' => $this->opslimit, 'memlimit' => $this->memlimit] = $this->validatedOptions(
            $options,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,
        );
    }

    /**
     * @param array{opslimit?: int, memlimit?: int} $options
     */
    public function hash(#[\SensitiveParameter] string $password, array $options = []): string
    {
        if (! $this->isValidPassword($password)) {
            throw new HashException('Invalid password provided.');
        }

        ['opslimit' => $opslimit, 'memlimit' => $memlimit] = $this->validatedOptions(
            $options,
            $this->opslimit,
            $this->memlimit,
        );

        return sodium_crypto_pwhash_str($password, $opslimit, $memlimit);
    }

    /**
     * @param array{opslimit?: int, memlimit?: int} $options
     */
    public function needsRehash(string $hash, array $options = []): bool
    {
        ['opslimit' => $opslimit, 'memlimit' => $memlimit] = $this->validatedOptions(
            $options,
            $this->opslimit,
            $this->memlimit,
        );

        return sodium_crypto_pwhash_str_needs_rehash($hash, $opslimit, $memlimit);
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
     * @return array{
     *  opslimit: int<SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, max>,
     *  memlimit: int<SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE, max>,
     * }
     *
     * @throws HashException
     */
    private function validatedOptions(array $options, int $opslimit, int $memlimit): array
    {
        $opslimit = $options['opslimit'] ?? $opslimit;
        $memlimit = $options['memlimit'] ?? $memlimit;

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

        return compact('opslimit', 'memlimit');
    }
}
