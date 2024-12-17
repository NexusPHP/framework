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

interface HashInterface
{
    public const MINIMUM_PASSWORD_LENGTH = 8;
    public const MAXIMUM_PASSWORD_LENGTH = 4096;

    /**
     * Creates a new password hash using a strong one-way hashing algorithm.
     *
     * @param array{
     *  cost?: int<4, 31>,
     *  opslimit?: int,
     *  memlimit?: int,
     *  memory_cost?: int,
     *  threads?: int,
     *  time_cost?: int,
     * } $options
     *
     * @throws HashException
     */
    public function hash(#[\SensitiveParameter] string $password, array $options = []): string;

    /**
     * Checks if the given hash matches the given options.
     *
     * @param array{
     *  cost?: int<4, 31>,
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     */
    public function needsRehash(string $hash, array $options = []): bool;

    /**
     * Verifies that a password matches a hash.
     */
    public function verify(#[\SensitiveParameter] string $password, string $hash): bool;

    /**
     * Checks if the hash driver can be used.
     */
    public function valid(): bool;

    /**
     * Checks if the password is without defects before hashing.
     */
    public function isValidPassword(#[\SensitiveParameter] string $password): bool;
}
