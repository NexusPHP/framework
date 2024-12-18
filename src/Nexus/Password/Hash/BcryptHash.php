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

final class BcryptHash extends AbstractHash
{
    public const int DEFAULT_COST = 12;
    public const int MINIMUM_COST = 4;
    public const int MAXIMUM_COST = 31;
    private const int MAXIMUM_BCRYPT_PASSWORD_LENGTH = 72;

    /**
     * @var int<self::MINIMUM_COST, self::MAXIMUM_COST>
     */
    private int $cost;

    /**
     * @param array{cost?: int} $options
     *
     * @throws HashException
     */
    public function __construct(
        public readonly Algorithm $algorithm,
        array $options = [],
    ) {
        if (Algorithm::Bcrypt !== $algorithm) {
            throw new HashException(\sprintf(
                'Algorithm expected to be Algorithm::Bcrypt, Algorithm::%s given.',
                $algorithm->name,
            ));
        }

        ['cost' => $this->cost] = $this->validatedCost($options, self::DEFAULT_COST);
    }

    /**
     * @param array{cost?: int} $options
     */
    public function hash(#[\SensitiveParameter] string $password, array $options = []): string
    {
        if (
            ! $this->isValidPassword($password)
            || \strlen($password) > self::MAXIMUM_BCRYPT_PASSWORD_LENGTH
        ) {
            throw new HashException('Invalid password provided.');
        }

        return password_hash(
            $password,
            $this->algorithm->value,
            $this->validatedCost($options, $this->cost),
        );
    }

    /**
     * @param array{cost?: int} $options
     */
    public function needsRehash(string $hash, array $options = []): bool
    {
        return password_needs_rehash(
            $hash,
            $this->algorithm->value,
            $this->validatedCost($options, $this->cost),
        );
    }

    public function verify(string $password, string $hash): bool
    {
        if (! $this->isValidPassword($password)) {
            return false;
        }

        if (\strlen($password) > self::MAXIMUM_BCRYPT_PASSWORD_LENGTH) {
            return false;
        }

        if (! str_starts_with($hash, '$2y')) {
            return false;
        }

        return password_verify($password, $hash);
    }

    public function valid(): bool
    {
        return \defined('PASSWORD_BCRYPT');
    }

    /**
     * @param array{cost?: int} $options
     *
     * @return array{cost: int<self::MINIMUM_COST, self::MAXIMUM_COST>}
     *
     * @throws HashException
     */
    private function validatedCost(array $options, int $cost): array
    {
        $cost = $options['cost'] ?? $cost;

        if (self::MINIMUM_COST > $cost || $cost > self::MAXIMUM_COST) {
            throw new HashException(\sprintf(
                'Algorithmic cost is expected to be between %d and %d, %d given.',
                self::MINIMUM_COST,
                self::MAXIMUM_COST,
                $cost,
            ));
        }

        return compact('cost');
    }
}
