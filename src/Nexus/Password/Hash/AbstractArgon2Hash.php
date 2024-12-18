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
    private const int MINIMUM_MEMORY_COST = 7 * 1024;
    private const int MINIMUM_TIME_COST = 1;
    private const int MINIMUM_THREADS = 1;

    /**
     * @var int<self::MINIMUM_MEMORY_COST, max>
     */
    private int $memoryCost;

    /**
     * @var int<self::MINIMUM_TIME_COST, max>
     */
    private int $timeCost;

    /**
     * @var int<self::MINIMUM_THREADS, max>
     */
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
        ['memory_cost' => $this->memoryCost, 'time_cost' => $this->timeCost, 'threads' => $this->threads] = $this->validatedOptions(
            $options,
            PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            PASSWORD_ARGON2_DEFAULT_TIME_COST,
            PASSWORD_ARGON2_DEFAULT_THREADS,
        );
    }

    /**
     * @param array{
     *  memory_cost?: int,
     *  threads?: int,
     *  time_cost?: int,
     * } $options
     */
    public function hash(#[\SensitiveParameter] string $password, array $options = []): string
    {
        if (! $this->isValidPassword($password)) {
            throw new HashException('Invalid password provided.');
        }

        return password_hash(
            $password,
            $this->algorithm->value,
            $this->validatedOptions(
                $options,
                $this->memoryCost,
                $this->timeCost,
                $this->threads,
            ),
        );
    }

    /**
     * @param array{
     *  memory_cost?: int,
     *  threads?: int,
     *  time_cost?: int,
     * } $options
     */
    public function needsRehash(string $hash, array $options = []): bool
    {
        return password_needs_rehash(
            $hash,
            $this->algorithm->value,
            $this->validatedOptions(
                $options,
                $this->memoryCost,
                $this->timeCost,
                $this->threads,
            ),
        );
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
     *  memory_cost: int<self::MINIMUM_MEMORY_COST, max>,
     *  time_cost: int<self::MINIMUM_TIME_COST, max>,
     *  threads: int<self::MINIMUM_THREADS, max>,
     * }
     *
     * @throws HashException
     */
    private function validatedOptions(array $options, int $memoryCost, int $timeCost, int $threads): array
    {
        $memoryCost = $options['memory_cost'] ?? $memoryCost;
        $timeCost = $options['time_cost'] ?? $timeCost;
        $threads = $options['threads'] ?? $threads;

        if ($memoryCost < self::MINIMUM_MEMORY_COST) {
            throw new HashException(\sprintf(
                'Memory cost should be %sKiB or greater, %sKiB given.',
                number_format(self::MINIMUM_MEMORY_COST / 1024),
                number_format($memoryCost / 1024),
            ));
        }

        if ($timeCost < self::MINIMUM_TIME_COST) {
            throw new HashException(\sprintf(
                'Time cost should be %d or greater, %d given.',
                self::MINIMUM_TIME_COST,
                $timeCost,
            ));
        }

        if ($threads < self::MINIMUM_THREADS) {
            throw new HashException(\sprintf(
                'Number of threads should be %d or greater, %d given.',
                self::MINIMUM_THREADS,
                $threads,
            ));
        }

        return [
            'memory_cost' => $memoryCost,
            'time_cost' => $timeCost,
            'threads' => $threads,
        ];
    }
}
