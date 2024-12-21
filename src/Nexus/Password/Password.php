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

namespace Nexus\Password;

final readonly class Password
{
    /**
     * Creates a password hash driver.
     *
     * @param non-empty-string $algo
     * @param array{
     *  cost?: int,
     *  iterations?: int,
     *  length?: int,
     *  opslimit?: int,
     *  memlimit?: int,
     *  memory_cost?: int,
     *  threads?: int,
     *  time_cost?: int,
     * } $options
     *
     * @throws HashException
     */
    public static function fromAlgorithm(string $algo, array $options = []): HashInterface
    {
        $algorithm = Algorithm::tryFrom($algo);

        return match ($algorithm) {
            Algorithm::Argon2i => new Hash\Argon2iHash($algorithm, $options),
            Algorithm::Argon2id => new Hash\Argon2idHash($algorithm, $options),
            Algorithm::Bcrypt => new Hash\BcryptHash($algorithm, $options),
            Algorithm::Pbkdf2HmacSha1,
            Algorithm::Pbkdf2HmacSha256,
            Algorithm::Pbkdf2HmacSha512 => new Hash\Pbkdf2Hash($algorithm, $options),
            Algorithm::Sodium => new Hash\SodiumHash($algorithm, $options),
            default => throw new HashException(\sprintf('Unsupported "%s" algorithm.', $algo)),
        };
    }
}
