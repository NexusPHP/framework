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

abstract class AbstractArgon2Hash extends AbstractHash
{
    private int $memoryCost;
    private int $timeCost;
    private int $threads;

    /**
     * @param array{
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     *
     * @throws HashException
     */
    public function __construct(
        public readonly Algorithm $algorithm,
        array $options = [],
    ) {
        $memoryCost = $options['memory_cost'] ?? PASSWORD_ARGON2_DEFAULT_MEMORY_COST;
        $timeCost = $options['time_cost'] ?? PASSWORD_ARGON2_DEFAULT_TIME_COST;
        $threads = $options['threads'] ?? PASSWORD_ARGON2_DEFAULT_THREADS;

        if ($memoryCost < PASSWORD_ARGON2_DEFAULT_MEMORY_COST) {
            throw new HashException(\sprintf(
                'Memory cost should be %s bytes or greater, %s bytes given.',
                number_format(PASSWORD_ARGON2_DEFAULT_MEMORY_COST),
                number_format($memoryCost),
            ));
        }

        if ($timeCost < PASSWORD_ARGON2_DEFAULT_TIME_COST) {
            throw new HashException(\sprintf(
                'Time cost should be %d or greater, %d given.',
                PASSWORD_ARGON2_DEFAULT_TIME_COST,
                $timeCost,
            ));
        }

        if ($threads < PASSWORD_ARGON2_DEFAULT_THREADS) {
            throw new HashException(\sprintf(
                'Number of threads should be %d or greater, %d given.',
                PASSWORD_ARGON2_DEFAULT_THREADS,
                $threads,
            ));
        }

        $this->memoryCost = $memoryCost;
        $this->timeCost = $timeCost;
        $this->threads = $threads;
    }

    public function hash(#[\SensitiveParameter] string $password, array $options = []): string
    {
        if (! $this->isValidPassword($password)) {
            throw new HashException('Invalid password provided.');
        }

        return password_hash($password, $this->algorithm->value, $this->options($options));
    }

    public function needsRehash(string $hash, array $options = []): bool
    {
        return password_needs_rehash($hash, $this->algorithm->value, $this->options($options));
    }

    public function verify(string $password, string $hash): bool
    {
        if (! $this->isValidPassword($password)) {
            return false;
        }

        return password_verify($password, $hash);
    }

    /**
     * @param array{
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     *
     * @return array{
     *  memory_cost: int,
     *  time_cost: int,
     *  threads: int,
     * }
     */
    private function options(array $options): array
    {
        return [
            'memory_cost' => $options['memory_cost'] ?? $this->memoryCost,
            'time_cost' => $options['time_cost'] ?? $this->timeCost,
            'threads' => $options['threads'] ?? $this->threads,
        ];
    }
}
