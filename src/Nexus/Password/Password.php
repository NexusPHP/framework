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

final class Password
{
    /**
     * Creates a password hash driver.
     *
     * @param array{
     *  cost?: int<4, 31>,
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     *
     * @throws HashException
     */
    public static function fromAlgorithm(Algorithm $algorithm, array $options = []): HashInterface
    {
        return match ($algorithm) {
            Algorithm::Argon2i => new Hash\Argon2iHash($algorithm, $options),
            Algorithm::Argon2id => new Hash\Argon2idHash($algorithm, $options),
            default => new Hash\BcryptHash($algorithm, $options),
        };
    }

    /**
     * @return array{
     *  algorithm: Algorithm,
     *  options: array{
     *      cost?: int,
     *      memory_cost?: int,
     *      time_cost?: int,
     *      threads?: int,
     *  }
     * }
     */
    public static function getInfo(string $hash): array
    {
        /**
         * @var array{
         *  algo: string|null,
         *  algoName: string,
         *  options: array{
         *      cost?: int,
         *      memory_cost?: int,
         *      time_cost?: int,
         *      threads?: int,
         *  }
         * } $info
         */
        $info = password_get_info($hash);
        $algo = $info['algo'];

        if (null === $algo) {
            throw new \InvalidArgumentException('Invalid hash provided.');
        }

        if (PASSWORD_BCRYPT === $algo) {
            return [
                'algorithm' => Algorithm::Bcrypt,
                'options' => [
                    'cost' => $info['options']['cost'] ?? Hash\BcryptHash::DEFAULT_COST,
                ],
            ];
        }

        return [
            'algorithm' => PASSWORD_ARGON2I === $algo ? Algorithm::Argon2i : Algorithm::Argon2id,
            'options' => [
                'memory_cost' => $info['options']['memory_cost'] ?? PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => $info['options']['time_cost'] ?? PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => $info['options']['threads'] ?? PASSWORD_ARGON2_DEFAULT_THREADS,
            ],
        ];
    }
}
